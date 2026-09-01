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
    
    <style>
        body { margin: 0; padding: 0; background-color: #f8f9fa; height: 100vh; display: flex; flex-direction: column; }
        .editor-header { background: #fff; padding: 10px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .editor-header .title { font-size: 1.1rem; font-weight: 600; margin: 0; display: flex; align-items: center;}
        #editor-container { flex: 1; display: flex; flex-direction: column; position: relative; }
        .tox-tinymce { border: none !important; border-top: 1px solid #ccc !important; }
        #loading-overlay { position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(255,255,255,0.8); display:flex; align-items:center; justify-content:center; z-index: 1000;}
    </style>
</head>
<body>

    <div class="editor-header">
        <div class="d-flex align-items-center">
            <a href="{{ url('data-budidaya') }}" class="btn btn-sm btn-light me-3"><i class="bi bi-arrow-left"></i> Kembali</a>
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
        <div>
            <button class="btn btn-primary btn-sm px-4" id="btn-save" disabled><i class="bi bi-save"></i> Save</button>
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
        <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        @if($ext == 'docx')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.21/mammoth.browser.min.js"></script>
            <script src="https://unpkg.com/html-docx-js/dist/html-docx.js"></script>
        @endif
        
        <script>
            let editorContent = `{!! $ext == 'html' ? str_replace('`', '\`', $content) : '' !!}`;
            
            async function initWordEditor() {
                if ("{{ $ext }}" === 'docx') {
                    try {
                        const response = await fetch("{{ asset('storage/'.$document->path) }}");
                        const arrayBuffer = await response.arrayBuffer();
                        const result = await mammoth.convertToHtml({arrayBuffer: arrayBuffer});
                        editorContent = result.value;
                    } catch(err) {
                        alert("Gagal membaca file DOCX.");
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
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
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
                        const response = await fetch("{{ asset('storage/'.$document->path) }}");
                        const arrayBuffer = await response.arrayBuffer();
                        const workbook = XLSX.read(arrayBuffer, {type: 'array'});
                        // Convert SheetJS workbook to x-spreadsheet data array
                        sheetData = stox(workbook);
                    } catch(err) {
                        alert("Gagal membaca file Excel/CSV.");
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
                url: "{{ url('data-budidaya/editor/' . $document->id) }}",
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
                url: "{{ url('data-budidaya/editor/' . $document->id) }}",
                type: 'POST', // Use POST with _method=PUT
                data: formData,
                processData: false,
                contentType: false,
                success: handleAjaxResponse,
                error: handleAjaxError
            });
        }
    </script>
</body>
</html>
