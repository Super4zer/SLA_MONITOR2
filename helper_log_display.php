<?php
/**
 * Helper: tampilkan preview HTML rich-content untuk kolom desorder/deslayan.
 * 
 * Cara pakai:
 *   include_once 'helper_log_display.php';
 *   // dalam loop tabel:
 *   echo renderLogColumn($rs['desorder'], 'desorder', $rs['idlog'], 'Uraian Order');
 *   echo renderLogColumn($rs['deslayan'], 'deslayan', $rs['idlog'], 'Aktivitas Layanan');
 */

function renderLogColumn($raw, $prefix, $idlog, $title) {
    $html  = stripslashes((string)$raw);
    $plain = trim(strip_tags($html));

    $divId = $prefix . '_' . $idlog;

    // Tampilkan teks (tanpa gambar)
    $textOnly = preg_replace('/<img[^>]*>/i', '', $html);
    $out  = "<div class='rich-preview'>{$textOnly}</div>";

    // Cari semua gambar dan tampilkan sebagai thumbnail klikable
    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
    if (!empty($matches[0])) {
        $out .= "<div class='img-thumbs'>";
        foreach ($matches[1] as $imgSrc) {
            $out .= "<img src='{$imgSrc}' class='img-thumb-preview' onclick=\"previewImage('{$imgSrc}')\" title='Klik untuk memperbesar' />";
        }
        $out .= "</div>";
    }

    // Simpan konten lengkap di hidden div untuk detail modal
    $out .= "<div id='konten_{$divId}' style='display:none;'>{$html}</div>";

    return $out;
}

function renderLogModal() {
    return '
<!-- Modal preview gambar -->
<div class="modal fade" id="imgPreviewModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title"><i class="fa fa-image"></i> Preview Gambar</h4>
      </div>
      <div class="modal-body" style="text-align: center; padding: 15px;">
        <img id="imgPreviewFull" src="" style="max-width: 100%; max-height: 75vh; height: auto;" />
      </div>
    </div>
  </div>
</div>

<style>
/* Rich HTML preview di kolom tabel - tampilkan semua teks */
.rich-preview { font-size: 13px; line-height: 1.5; color: #555; word-break: break-word; }
.rich-preview p { margin: 0 0 2px 0; }
.rich-preview strong, .rich-preview b { font-weight: bold; }
.rich-preview em { font-style: italic; }
.rich-preview u { text-decoration: underline; }
.rich-preview s { text-decoration: line-through; }
.rich-preview a { color: #337ab7; text-decoration: underline; }
.rich-preview ul, .rich-preview ol { margin: 0 0 2px 14px; padding: 0; }
.rich-preview li { margin-bottom: 1px; }

/* Kolom Order dan Order Layanan - lebar tetap */
#contoh th:nth-child(9), #contoh td:nth-child(9),
#contoh th:nth-child(10), #contoh td:nth-child(10) {
    width: 200px !important;
    min-width: 200px !important;
    max-width: 200px !important;
}

/* Thumbnail gambar di kolom tabel */
.img-thumbs { margin-top: 4px; display: flex; flex-wrap: wrap; gap: 4px; }
.img-thumb-preview {
    width: 50px; height: 50px;
    object-fit: cover;
    border: 1px solid #ddd;
    border-radius: 3px;
    cursor: pointer;
}

/* Gambar di tabel (fallback) */
#contoh td img:not(.img-thumb-preview) { max-width: 50px; max-height: 50px; cursor: pointer; border: 1px solid #ddd; border-radius: 3px; padding: 2px; }
</style>

<script>
// Preview gambar - klik thumbnail untuk membuka modal besar
function previewImage(src) {
    document.getElementById("imgPreviewFull").src = src;
    $("#imgPreviewModal").modal("show");
}
$("#imgPreviewModal").on("hidden.bs.modal", function() { 
    document.getElementById("imgPreviewFull").src = ""; 
});
</script>';
}
?>
