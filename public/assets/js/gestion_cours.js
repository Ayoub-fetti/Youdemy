function openEditModal(courseId, currentTags) {
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editCourseId').value = courseId;
    
    // Reset all checkboxes
    document.querySelectorAll('.tag-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Check the current tags
    if (currentTags && currentTags.trim() !== '') {
        const tagNames = currentTags.split(',').map(t => t.trim());
        document.querySelectorAll('.tag-checkbox').forEach(checkbox => {
            const label = checkbox.nextElementSibling.textContent.trim();
            if (tagNames.includes(label)) {
                checkbox.checked = true;
            }
        });
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function deleteTag(tagId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce tag? Cette action supprimera également toutes les associations avec les cours.')) {
        document.getElementById('deleteTagId').value = tagId;
        document.getElementById('deleteTagForm').submit();
    }
}