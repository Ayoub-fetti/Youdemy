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

// Gestion des tags
const tagsInput = document.getElementById('tags');
const tagContainer = document.getElementById('tag-container');
const tagsListInput = document.getElementById('tags_list');
let tags = [];

function updateTags() {
    tagContainer.innerHTML = '';
    tags.forEach((tag, index) => {
        const tagElement = document.createElement('span');
        tagElement.className = 'tag';
        tagElement.innerHTML = `
            <span>${tag}</span>
            <button type="button" onclick="removeTag(${index})">&times;</button>
        `;
        tagContainer.appendChild(tagElement);
    });
    tagsListInput.value = tags.join(',');
}

function addTag(tag) {
    tag = tag.trim();
    if (tag && !tags.includes(tag)) {
        tags.push(tag);
        updateTags();
    }
    tagsInput.value = '';
}

function removeTag(index) {
    tags.splice(index, 1);
    updateTags();
}

tagsInput.addEventListener('keyup', (e) => {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        const tag = tagsInput.value.replace(',', '');
        addTag(tag);
    }
});