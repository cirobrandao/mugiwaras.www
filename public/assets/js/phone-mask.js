document.addEventListener('DOMContentLoaded', () => {
  const inputs = document.querySelectorAll('[data-mask="phone"]');
  inputs.forEach((input) => {
    input.addEventListener('input', () => {
      let v = input.value.replace(/\D/g, '').slice(0, 11);
      if (v.length >= 3) {
        v = v.replace(/(\d{2})(\d)/, '$1 $2');
      }
      if (v.length >= 7) {
        v = v.replace(/(\d{2})\s(\d)(\d{4})(\d{0,4})/, '$1 $2 $3-$4');
      }
      input.value = v;
    });
  });
});
