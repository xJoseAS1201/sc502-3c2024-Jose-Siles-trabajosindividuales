
function cargarComentarios(taskId) {
    fetch(`backend/comments.php?task_id=${taskId}`)
      .then(res => res.json())
      .then(data => {
        const contenedor = document.getElementById(`comentarios-${taskId}`);
        contenedor.innerHTML = '';
        if (data.status === 'success') {
          data.comments.forEach(c => {
            const div = document.createElement('div');
            div.className = 'comentario';
            div.innerHTML = `
              <p><strong>${c.username}:</strong> ${c.comment}</p>
              <button onclick="eliminarComentario(${c.id}, ${taskId})">Eliminar</button>
            `;
            contenedor.appendChild(div);
          });
        }
      });
  }
  function agregarComentario(taskId, texto) {
    fetch('backend/comments.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({task_id: taskId, comment: texto})
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        cargarComentarios(taskId);
      }
    });
  }
  function editarComentario(commentId, nuevoTexto) {
    fetch('backend/comments.php', {
      method: 'PUT',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({id: commentId, comment: nuevoTexto})
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
      }
    });
  }
  function eliminarComentario(commentId) {
  fetch('backend/comments.php', {
    method: 'DELETE',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id: commentId})
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
    }
  });
}
