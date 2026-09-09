<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit: {{ $document->original_name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    @if(in_array($ext, ['json', 'xlsx', 'xls', 'csv']))
        <!-- x-spreadsheet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/x-data-spreadsheet@1.1.9/dist/xspreadsheet.css">
    @endif
    
    <link href="{{ asset('assets/css/editor.css') }}" rel="stylesheet">
</head>
<body>

    <div class="editor-header">
        <div class="d-flex align-items-center">
            <a href="{{ url('data-File') }}" class="btn btn-sm btn-light me-3"><i class="bi bi-arrow-left"></i> Kembali</a>
            <div class="title">
                @if(in_array($ext, ['html', 'docx']))
                    <i class="bi bi-file-earmark-word-fill text-primary me-2 fs-4"></i>
                @else
                    <i class="bi bi-file-earmark-excel-fill text-success me-2 fs-4"></i>
                @endif
                {{ $document->original_name }}
                <span class="badge bg-secondary ms-3" id="save-status">Memuat...</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('data-File/download/' . $document->id) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-cloud-arrow-down"></i> Unduh File Asli</a>
            <button class="btn btn-primary btn-sm px-4" id="btn-save" disabled><i class="bi bi-save"></i> Simpan Perubahan</button>
        </div>
    </div>

    <div id="editor-container">
        <div id="loading-overlay">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
        
        @if(in_array($ext, ['html', 'docx']))
            <textarea id="tinymce-editor"></textarea>
        @else
            <div id="xspreadsheet-editor"></div>
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @if(in_array($ext, ['html', 'docx']))
        <!-- TinyMCE & Mammoth for Word -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
        @if($ext == 'docx')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.21/mammoth.browser.min.js"></script>
            <script src="https://unpkg.com/html-docx-js/dist/html-docx.js"></script>
        @endif
        
        <script>
            let editorContent = `{!! $ext == 'html' ? str_replace('`', '\`', $content) : '' !!}`;
            
            async function initWordEditor() {
                if ("{{ $ext }}" === 'docx') {
                    try {
                        const response = await fetch("{{ url('data-File/download/'.$document->id) }}");
                        const arrayBuffer = await response.arrayBuffer();
                        const result = await mammoth.convertToHtml({arrayBuffer: arrayBuffer});
                        editorContent = result.value || '<p></p>';
                    } catch(err) {
                        alert("Gagal membaca isi file Word.");
                        console.error(err);
                    }
                }

                tinymce.init({
                    selector: '#tinymce-editor',
                    height: '100%',
                    resize: false,
                    menubar: 'file edit view insert format tools table help',
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                    toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px; padding: 20px; }',
                    setup: function (editor) {
                        editor.on('init', function () {
                            editor.setContent(editorContent);
                            $('#loading-overlay').hide();
                            $('#btn-save').prop('disabled', false);
                            $('#save-status').text('Tersimpan').removeClass('bg-warning').addClass('bg-success');
                        });
                        editor.on('change', function () {
                            $('#save-status').text('Belum disimpan').removeClass('bg-secondary bg-success').addClass('bg-warning text-dark');
                        });
                    }
                });
            }

            initWordEditor();

            $('#btn-save').click(function() {
                let htmlData = tinymce.activeEditor.getContent();
                
                if ("{{ $ext }}" === 'docx') {
                    // Convert HTML back to DOCX Blob
                    let contentToConvert = "<!DOCTYPE html><html><body>" + htmlData + "</body></html>";
                    let docxBlob = htmlDocx.asBlob(contentToConvert);
                    saveBinaryData(docxBlob, 'file.docx');
                } else {
                    saveTextData(htmlData);
                }
            });
        </script>
    @else
        <!-- x-spreadsheet & SheetJS for Excel -->
        <script src="https://unpkg.com/x-data-spreadsheet@1.1.9/dist/xspreadsheet.js"></script>
        <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
        <script src="https://cdn.sheetjs.com/xspreadsheet/xlsxspread.js"></script>
        
        <script>
            let s;
            async function initExcelEditor() {
                let sheetData = {};
                
                if ("{{ $ext }}" === 'json') {
                    try {
                        let content = `{!! str_replace('`', '\`', $content) !!}`;
                        if(content && content !== "{}") {
                            sheetData = JSON.parse(content);
                        }
                    } catch(e) { console.error(e); }
                } else {
                    // Load XLSX/XLS/CSV
                    try {
                        const response = await fetch("{{ url('data-File/download/'.$document->id) }}");
                        const arrayBuffer = await response.arrayBuffer();
                        const workbook = XLSX.read(arrayBuffer, {type: 'array'});
                        // Convert SheetJS workbook to x-spreadsheet data array
                        sheetData = stox(workbook);
                    } catch(err) {
                        alert("Gagal membaca isi file Excel/CSV.");
                        console.error(err);
                    }
                }

                s = new x_spreadsheet("#xspreadsheet-editor", {
                    showToolbar: true,
                    showGrid: true,
                }).loadData(sheetData);

                $('#loading-overlay').hide();
                $('#btn-save').prop('disabled', false);
                $('#save-status').text('Tersimpan').removeClass('bg-warning').addClass('bg-success');

                s.change(function(data) {
                    $('#save-status').text('Belum disimpan').removeClass('bg-secondary bg-success').addClass('bg-warning text-dark');
                });
            }

            initExcelEditor();

            $('#btn-save').click(function() {
                if ("{{ $ext }}" === 'json') {
                    saveTextData(JSON.stringify(s.getData()));
                } else {
                    // Convert x-spreadsheet data back to SheetJS workbook
                    let wb = xtos(s.getData());
                    // Generate XLSX array buffer
                    let out = XLSX.write(wb, { bookType: "{{ $ext }}" === 'csv' ? 'csv' : 'xlsx', type: "array" });
                    let blob = new Blob([out], { type: "application/octet-stream" });
                    saveBinaryData(blob, "file.{{ $ext }}");
                }
            });
        </script>
    @endif

    <script>
        function setSavingState() {
            $('#save-status').text('Menyimpan...').removeClass('bg-warning bg-success text-dark').addClass('bg-primary text-white');
            $('#btn-save').prop('disabled', true);
        }

        function handleAjaxResponse(res) {
            $('#save-status').text('Tersimpan').removeClass('bg-primary text-white text-dark').addClass('bg-success text-white');
            $('#btn-save').prop('disabled', false);
        }

        function handleAjaxError(err) {
            $('#save-status').text('Gagal Menyimpan').removeClass('bg-primary').addClass('bg-danger');
            $('#btn-save').prop('disabled', false);
            alert('Gagal menyimpan dokumen!');
        }

        function saveTextData(content) {
            setSavingState();
            $.ajax({
                url: "{{ url('data-file/editor/' . $document->id) }}",
                type: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    content: content
                },
                success: handleAjaxResponse,
                error: handleAjaxError
            });
        }

        function saveBinaryData(blob, filename) {
            setSavingState();
            let formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('_method', 'PUT'); // Fake PUT for FormData in Laravel
            formData.append('file', blob, filename);

            $.ajax({
                url: "{{ url('data-file/editor/' . $document->id) }}",
                type: 'POST', // Use POST with _method=PUT
                data: formData,
                processData: false,
                contentType: false,
                success: handleAjaxResponse,
                error: handleAjaxError
            });
        }

        @if(session('sharefile_unlocked') && (!Auth::check() || Auth::user()->role !== 'admin'))
        (function() {
            const IDLE_TIMEOUT_MS = 5 * 60 * 1000;
            const PING_INTERVAL_MS = 60 * 1000;
            const LOCK_URL = "{{ url('/data-File/lock') }}";
            const PING_URL = "{{ url('/data-File/ping-activity') }}";
            const CSRF_TOKEN = "{{ csrf_token() }}";

            let lastActivityTime = Date.now();
            let isLockedTriggered = false;
            let hasInteractedSinceLastPing = false;

            function resetActivity() {
                if (isLockedTriggered) return;
                lastActivityTime = Date.now();
                hasInteractedSinceLastPing = true;
            }

            const activityEvents = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'];
            activityEvents.forEach(function(evt) {
                window.addEventListener(evt, resetActivity, { passive: true });
            });

            const checkInterval = setInterval(function() {
                if (isLockedTriggered) return;
                if (Date.now() - lastActivityTime >= IDLE_TIMEOUT_MS) {
                    triggerAutoLock();
                }
            }, 1000);

            const pingInterval = setInterval(function() {
                if (isLockedTriggered) return;
                if (hasInteractedSinceLastPing) {
                    hasInteractedSinceLastPing = false;
                    fetch(PING_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data && data.locked) {
                            triggerAutoLock();
                        }
                    })
                    .catch(function() {});
                }
            }, PING_INTERVAL_MS);

            document.addEventListener('visibilitychange', function() {
                if (!document.hidden && !isLockedTriggered) {
                    if (Date.now() - lastActivityTime >= IDLE_TIMEOUT_MS) {
                        triggerAutoLock();
                    }
                }
            });

            function triggerAutoLock() {
                if (isLockedTriggered) return;
                isLockedTriggered = true;
                clearInterval(checkInterval);
                clearInterval(pingInterval);

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = LOCK_URL;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = CSRF_TOKEN;
                form.appendChild(csrfInput);

                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = 'idle';
                form.appendChild(reasonInput);

                document.body.appendChild(form);
                form.submit();
            }
        })();
        @endif
    </script>
</body>
</html>
