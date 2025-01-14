    // fonction pour suspendre un utilisateur
function toggleUserStatus(userId) {
  fetch(`../../public/assets/php/toggle_status.php?user_id=${userId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const statusElement = document.getElementById(`status-${userId}`);
        const statusSpan = statusElement.querySelector('span');
        statusSpan.classList.remove('bg-green-300', 'bg-red-300');
        statusSpan.classList.add(data.newStatus === 'actif' ? 'bg-green-300' : 'bg-red-300');
        statusSpan.textContent = data.newStatus;
      } else {
        alert('Failed to toggle status');
      }
    })
    .catch(error => console.error('Error:', error));
}

// fonction pour la recherche en searchInput
document.getElementById('searchInput').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  const rows = document.querySelectorAll('.transaction-row');
  
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    if (text.includes(searchTerm)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
});

// Fonction pour confirmer la suppression d'un utilisateur
document.addEventListener('DOMContentLoaded', function() {
  const deleteButtons = document.querySelectorAll('button[name="delete_user"]');
  
  deleteButtons.forEach(button => {
    button.addEventListener('click', function(event) {
      const confirmation = window.confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.');
      
      if (!confirmation) {
        event.preventDefault();
      }
    });
  });
});