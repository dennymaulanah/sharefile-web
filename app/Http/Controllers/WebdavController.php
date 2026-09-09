<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebdavController extends Controller
{
    /**
     * Handle incoming WebDAV requests from MS Office, WebDAV clients, etc.
     */
    public function handle(Request $request, $path = '')
    {
        $path = trim(urldecode($path), '/');
        // Handle backslash from Windows clients if present
        $path = str_replace('\\', '/', $path);
        
        $method = strtoupper($request->getMethod());

        switch ($method) {
            case 'OPTIONS':
                return $this->handleOptions($request, $path);
            case 'PROPFIND':
                return $this->handlePropfind($request, $path);
            case 'HEAD':
            case 'GET':
                return $this->handleGet($request, $path);
            case 'LOCK':
                return $this->handleLock($request, $path);
            case 'UNLOCK':
                return $this->handleUnlock($request, $path);
            case 'PUT':
                return $this->handlePut($request, $path);
            case 'PROPPATCH':
                return $this->handleProppatch($request, $path);
            case 'DELETE':
                return $this->handleDelete($request, $path);
            default:
                return response('Method Not Allowed', 405, [
                    'Allow' => 'OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, PROPFIND, PROPPATCH, COPY, MOVE, LOCK, UNLOCK',
                ]);
        }
    }

    /**
     * Respond to OPTIONS request. Crucial for MS Office to recognize WebDAV support.
     */
    private function handleOptions(Request $request, $path)
    {
        return response('', 200, [
            'DAV' => '1, 2',
            'MS-Author-Via' => 'DAV',
            'Allow' => 'OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, PROPFIND, PROPPATCH, COPY, MOVE, LOCK, UNLOCK',
            'Public' => 'OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, PROPFIND, PROPPATCH, COPY, MOVE, LOCK, UNLOCK',
            'Accept-Ranges' => 'bytes',
            'Content-Length' => '0',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Respond to PROPFIND request. Returns file properties in WebDAV XML format.
     */
    private function handlePropfind(Request $request, $path)
    {
        $disk = Storage::disk('public');
        
        // Check if root or empty
        $isRoot = empty($path);
        $fullPath = $disk->path($path);
        $exists = $isRoot || $disk->exists($path) || is_dir($fullPath);

        if (!$exists) {
            return response('File tidak ditemukan', 404);
        }

        $isDir = $isRoot || is_dir($fullPath);
        $href = url('webdav/' . ($path ? rawurlencode($path) : ''));
        $filename = $isRoot ? 'Share-Budidaya' : basename($path);

        $size = $isDir ? 0 : ($disk->exists($path) ? $disk->size($path) : (file_exists($fullPath) ? filesize($fullPath) : 0));
        $lastModifiedTimestamp = $isDir ? (file_exists($fullPath) ? filemtime($fullPath) : time()) : ($disk->exists($path) ? $disk->lastModified($path) : filemtime($fullPath));
        $lastModified = gmdate('D, d M Y H:i:s GMT', $lastModifiedTimestamp);
        $creation = gmdate('Y-m-d\TH:i:s\Z', $lastModifiedTimestamp);
        $mime = $isDir ? 'httpd/unix-directory' : ($disk->mimeType($path) ?: 'application/octet-stream');

        $resourceType = $isDir ? '<D:collection/>' : '';

        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<D:multistatus xmlns:D="DAV:">' . "\n" .
            '  <D:response>' . "\n" .
            '    <D:href>' . htmlspecialchars($href, ENT_XML1, 'UTF-8') . '</D:href>' . "\n" .
            '    <D:propstat>' . "\n" .
            '      <D:prop>' . "\n" .
            '        <D:displayname>' . htmlspecialchars($filename, ENT_XML1, 'UTF-8') . '</D:displayname>' . "\n" .
            '        <D:getcontentlength>' . $size . '</D:getcontentlength>' . "\n" .
            '        <D:getlastmodified>' . $lastModified . '</D:getlastmodified>' . "\n" .
            '        <D:creationdate>' . $creation . '</D:creationdate>' . "\n" .
            '        <D:getcontenttype>' . htmlspecialchars($mime, ENT_XML1, 'UTF-8') . '</D:getcontenttype>' . "\n" .
            '        <D:resourcetype>' . $resourceType . '</D:resourcetype>' . "\n" .
            '        <D:supportedlock>' . "\n" .
            '          <D:lockentry>' . "\n" .
            '            <D:lockscope><D:exclusive/></D:lockscope>' . "\n" .
            '            <D:locktype><D:write/></D:locktype>' . "\n" .
            '          </D:lockentry>' . "\n" .
            '        </D:supportedlock>' . "\n" .
            '        <D:lockdiscovery/>' . "\n" .
            '      </D:prop>' . "\n" .
            '      <D:status>HTTP/1.1 200 OK</D:status>' . "\n" .
            '    </D:propstat>' . "\n" .
            '  </D:response>' . "\n";

        // If directory and Depth header is 1, list items
        $depth = $request->header('Depth', '0');
        if ($isDir && $depth === '1') {
            $files = $disk->files($path);
            foreach ($files as $file) {
                $fileHref = url('webdav/' . rawurlencode($file));
                $fileBase = basename($file);
                $fSize = $disk->size($file);
                $fMod = gmdate('D, d M Y H:i:s GMT', $disk->lastModified($file));
                $fCre = gmdate('Y-m-d\TH:i:s\Z', $disk->lastModified($file));
                $fMime = $disk->mimeType($file) ?: 'application/octet-stream';

                $xml .= '  <D:response>' . "\n" .
                    '    <D:href>' . htmlspecialchars($fileHref, ENT_XML1, 'UTF-8') . '</D:href>' . "\n" .
                    '    <D:propstat>' . "\n" .
                    '      <D:prop>' . "\n" .
                    '        <D:displayname>' . htmlspecialchars($fileBase, ENT_XML1, 'UTF-8') . '</D:displayname>' . "\n" .
                    '        <D:getcontentlength>' . $fSize . '</D:getcontentlength>' . "\n" .
                    '        <D:getlastmodified>' . $fMod . '</D:getlastmodified>' . "\n" .
                    '        <D:creationdate>' . $fCre . '</D:creationdate>' . "\n" .
                    '        <D:getcontenttype>' . htmlspecialchars($fMime, ENT_XML1, 'UTF-8') . '</D:getcontenttype>' . "\n" .
                    '        <D:resourcetype/>' . "\n" .
                    '      </D:prop>' . "\n" .
                    '      <D:status>HTTP/1.1 200 OK</D:status>' . "\n" .
                    '    </D:propstat>' . "\n" .
                    '  </D:response>' . "\n";
            }
        }

        $xml .= '</D:multistatus>';

        return response($xml, 207, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'DAV' => '1, 2',
            'MS-Author-Via' => 'DAV',
        ]);
    }

    /**
     * Respond to GET/HEAD request. Streams the file with DAV authoring headers.
     */
    private function handleGet(Request $request, $path)
    {
        $disk = Storage::disk('public');
        $fullPath = $disk->path($path);

        if (!file_exists($fullPath) || is_dir($fullPath)) {
            return response('File tidak ditemukan', 404);
        }

        $size = filesize($fullPath);
        $lastModified = gmdate('D, d M Y H:i:s GMT', filemtime($fullPath));
        $etag = '"' . md5_file($fullPath) . '"';
        $mime = $disk->mimeType($path) ?: 'application/octet-stream';

        $headers = [
            'Content-Type' => $mime,
            'Content-Length' => $size,
            'Last-Modified' => $lastModified,
            'ETag' => $etag,
            'DAV' => '1, 2',
            'MS-Author-Via' => 'DAV',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache, must-revalidate',
        ];

        if ($request->isMethod('HEAD')) {
            return response('', 200, $headers);
        }

        return response()->file($fullPath, $headers);
    }

    /**
     * Respond to LOCK request. MS Office locks the file before editing.
     */
    private function handleLock(Request $request, $path)
    {
        $token = 'opaquelocktoken:' . Str::uuid()->toString();
        $href = url('webdav/' . ($path ? rawurlencode($path) : ''));

        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<D:prop xmlns:D="DAV:">' . "\n" .
            '  <D:lockdiscovery>' . "\n" .
            '    <D:activelock>' . "\n" .
            '      <D:locktype><D:write/></D:locktype>' . "\n" .
            '      <D:lockscope><D:exclusive/></D:lockscope>' . "\n" .
            '      <D:depth>0</D:depth>' . "\n" .
            '      <D:owner><D:href>' . htmlspecialchars($request->ip(), ENT_XML1, 'UTF-8') . '</D:href></D:owner>' . "\n" .
            '      <D:timeout>Second-3600</D:timeout>' . "\n" .
            '      <D:locktoken><D:href>' . $token . '</D:href></D:locktoken>' . "\n" .
            '      <D:lockroot><D:href>' . htmlspecialchars($href, ENT_XML1, 'UTF-8') . '</D:href></D:lockroot>' . "\n" .
            '    </D:activelock>' . "\n" .
            '  </D:lockdiscovery>' . "\n" .
            '</D:prop>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Lock-Token' => '<' . $token . '>',
            'DAV' => '1, 2',
            'MS-Author-Via' => 'DAV',
        ]);
    }

    /**
     * Respond to UNLOCK request. Released when MS Office closes the document.
     */
    private function handleUnlock(Request $request, $path)
    {
        return response('', 204, [
            'DAV' => '1, 2',
            'MS-Author-Via' => 'DAV',
        ]);
    }

    /**
     * Respond to PUT request. This is triggered when the user presses Ctrl+S in Word / Excel!
     */
    private function handlePut(Request $request, $path)
    {
        // Get raw body
        $content = $request->getContent();
        if ($content === null || $content === '') {
            $content = file_get_contents('php://input');
        }

        $disk = Storage::disk('public');
        
        // Ensure parent directory exists
        $dir = dirname($path);
        if ($dir && $dir !== '.' && !$disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }

        // Write directly to disk
        $disk->put($path, $content);
        $size = strlen($content);

        // Update database record if exists
        $doc = Document::where('path', $path)->first();
        if (!$doc) {
            // Also check by filename or matching path
            $doc = Document::where('filename', basename($path))->first();
        }

        if ($doc) {
            $doc->update([
                'file_size' => $size,
            ]);
        } else {
            // If the document wasn't registered in database, register it now
            $filename = basename($path);
            Document::create([
                'original_name' => $filename,
                'filename' => $filename,
                'path' => $path,
                'file_size' => $size,
                'mime_type' => $disk->mimeType($path) ?: 'application/octet-stream',
                'owner_name' => $request->ip(),
                'is_folder' => false,
                'parent_id' => null,
            ]);
        }

        return response('', 200, [
            'DAV' => '1, 2',
            'MS-Author-Via' => 'DAV',
            'ETag' => '"' . md5($content) . '"',
        ]);
    }

    /**
     * Handle PROPPATCH request.
     */
    private function handleProppatch(Request $request, $path)
    {
        $href = url('webdav/' . ($path ? rawurlencode($path) : ''));
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<D:multistatus xmlns:D="DAV:">' . "\n" .
            '  <D:response>' . "\n" .
            '    <D:href>' . htmlspecialchars($href, ENT_XML1, 'UTF-8') . '</D:href>' . "\n" .
            '    <D:propstat>' . "\n" .
            '      <D:status>HTTP/1.1 200 OK</D:status>' . "\n" .
            '    </D:propstat>' . "\n" .
            '  </D:response>' . "\n" .
            '</D:multistatus>';

        return response($xml, 207, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'DAV' => '1, 2',
            'MS-Author-Via' => 'DAV',
        ]);
    }

    /**
     * Handle DELETE request.
     */
    private function handleDelete(Request $request, $path)
    {
        $disk = Storage::disk('public');
        if ($disk->exists($path)) {
            $disk->delete($path);
            $doc = Document::where('path', $path)->first();
            if ($doc) {
                $doc->delete();
            }
            return response('', 204);
        }

        return response('File tidak ditemukan', 404);
    }
}
