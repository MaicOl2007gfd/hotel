function cambiar(tipo, valor) {
  const elemento = document.getElementById(tipo);
  if (!elemento) return;

  let numero = parseInt(elemento.innerText, 10) || 0;
  numero += valor;

  // Validar rango de adultos
  if (tipo === 'adultos') {
    if (numero < 1) numero = 1;
    if (numero > 4) {
      alert('El máximo de personas por habitación es 4');
      return;
    }
  }

  elemento.innerText = numero;
  actualizarTotalHuespedes();
}

function actualizarTotalHuespedes() {
  const adultos = parseInt(document.getElementById('adultos').innerText, 10) || 1;
  const totalInput = document.getElementById('total_huespedes');
  if (totalInput) {
    totalInput.value = adultos;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  actualizarTotalHuespedes();
});
