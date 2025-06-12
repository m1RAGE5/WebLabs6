    // видалення нулів
    function no_zeros(arr) {
        let filtered = arr.filter(num => num !== 0);
        arr.length = 0;
        arr.push(...filtered);
    }

    function runTest() {
        let testArray = [9, 0, 9, 0, 4, 5, 0, 9];
        no_zeros(testArray);
        document.getElementById('result').innerText = 'Масив без нулів: ' + testArray.join(', ');
    }

    // дата
    function getFormattedDate() {
      const days = ['Неділя', 'Понеділок', 'Вівторок', 'Середа', 'Четвер', 'П’ятниця', 'Субота'];
      const d = new Date();
      const dd = String(d.getDate()).padStart(2, '0');
      const mm = String(d.getMonth() + 1).padStart(2, '0');
      return `${days[d.getDay()]}, ${dd}/${mm}/${d.getFullYear()}`;
    }

    // форма
    function showForm() {
        document.getElementById('contact-form').style.display = 'block';
    }
    
    function validateForm() {
        let isValid = true;
        const get = id => document.getElementById(id).value.trim();
      const setErr = (id, msg) => (document.getElementById(id + 'Error').innerText = msg);

      const regexes = {
        name: /^[A-Za-zА-Яа-я]{3,}$/,
        notDigitStart: /^[^0-9]/,
        zip: /^\d{5}$/,
        phone: /^\+38\(\d{3}\)\d{3}-\d{2}-\d{2}$/,
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        password: /^(?=.*\d).{8,}$/,
        address: /^вул\.\s*\S+(?:\s+\S+)*\s+\d+,\s*\S+(?:\s+\S+)*,\s*\S+(?:\s+\S+)*$/
      };

      const fields = ['firstName', 'lastName', 'address', 'zipCode', 'phone', 'email', 'password'];
      const values = Object.fromEntries(fields.map(f => [f, get(f)]));

      setErr('firstName', '');
      setErr('lastName', '');
      setErr('address', '');
      setErr('zipCode', '');
      setErr('phone', '');
      setErr('email', '');
      setErr('password', '');

      if (!regexes.name.test(values.firstName) || !regexes.notDigitStart.test(values.firstName)) {
        setErr('firstName', 'Ім’я повинно містити літери (мін. 3) без цифр');
        valid = false;
      }

      if (!regexes.name.test(values.lastName)) {
        setErr('lastName', 'Прізвище повинно містити літери (мін. 3) без цифр');
        valid = false;
      }

      if (!regexes.address.test(values.address)) {
        setErr('address', 'Формат: вул. Назва Номер, Місто, Країна');
        valid = false;
      }

      if (!regexes.zip.test(values.zipCode)) {
        setErr('zipCode', 'Індекс повинен містити 5 цифр');
        valid = false;
      }

      if (!regexes.phone.test(values.phone)) {
        setErr('phone', 'Телефон у форматі: +38(XXX)XXX-XX-XX');
        valid = false;
      }

      if (!regexes.email.test(values.email)) {
        setErr('email', 'Некоректний email');
        valid = false;
      }

      if (!regexes.password.test(values.password)) {
        setErr('password', 'Пароль мін. 8 символів і хоча б одна цифра');
        valid = false;
      }

      if (valid) alert('Форма успішно відправлена!');
    }

    // при завантаженні
    $(document).ready(() => {
      $('#current-date').text(getFormattedDate());
    });