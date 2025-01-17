document.getElementById('type_cours').addEventListener('change', function() {
    const pdfUpload = document.getElementById('pdf_upload');
    const videoUrl = document.getElementById('video_url');
    
    if (this.value === 'pdf') {
        pdfUpload.classList.remove('hidden');
        videoUrl.classList.add('hidden');
        document.getElementById('fichier_pdf').required = true;
        document.getElementById('url_video').required = false;
    } else if (this.value === 'video') {
        pdfUpload.classList.add('hidden');
        videoUrl.classList.remove('hidden');
        document.getElementById('fichier_pdf').required = false;
        document.getElementById('url_video').required = true;
    } else {
        pdfUpload.classList.add('hidden');
        videoUrl.classList.add('hidden');
        document.getElementById('fichier_pdf').required = false;
        document.getElementById('url_video').required = false;
    }
});