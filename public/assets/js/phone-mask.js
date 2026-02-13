document.addEventListener('DOMContentLoaded', () => {
  const inputs = document.querySelectorAll('[data-mask="phone"]');
  const onlyDigits = (value) => (value || '').replace(/\D/g, '');

  const formatBrazilPhone = (digits) => {
    let v = digits.slice(0, 11);
    if (v.length >= 3) {
      v = v.replace(/(\d{2})(\d)/, '$1 $2');
    }
    if (v.length >= 7) {
      v = v.replace(/(\d{2})\s(\d)(\d{4})(\d{0,4})/, '$1 $2 $3-$4');
    }
    return v;
  };

  inputs.forEach((input) => {
    const form = input.closest('form');
    const countryInput = form ? form.querySelector('input[name="phone_country"]') : null;

    const applyMask = () => {
      const ddi = countryInput ? onlyDigits(countryInput.value) : '55';
      const isBrazil = ddi === '' || ddi === '55';
      const maxDigits = isBrazil ? 11 : 15;
      const digits = onlyDigits(input.value).slice(0, maxDigits);
      input.value = isBrazil ? formatBrazilPhone(digits) : digits;
      input.setAttribute('maxlength', isBrazil ? '14' : '15');
      input.setAttribute('inputmode', 'numeric');
    };

    input.addEventListener('input', applyMask);
    if (countryInput) {
      countryInput.addEventListener('input', () => {
        countryInput.value = onlyDigits(countryInput.value).slice(0, 4);
        applyMask();
      });
      countryInput.setAttribute('maxlength', '4');
      countryInput.setAttribute('inputmode', 'numeric');
    }
    applyMask();
  });
});
