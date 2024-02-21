document.addEventListener('DOMContentLoaded', function() {
    const callbackFormElement = document.createElement('div');
    const callbackFormTemplate = `
        <div id="callback-form__close-button" class="callback-form__close-button">
            <div>
                <div class="leftright"></div>
                <div class="rightleft"></div>
                <span class="close-btn">закрити</span>
            </div>
        </div>        
        <form id="callback-form" class="callback-form">
            <h2>Бажаєте, щоб ми вам зателефонували?</h2>
            <p>Введіть ваш номер телефону. На нього буде надіслано СМС код для перевірки!</p>
            <label for="phone">Номер телефону:</label>
            <input type="tel" id="callback-form__phone" class="callback-phone" name="phone" required>
            <button type="submit" id="callback-form__submit-button" class="callback-form__submit-button">Надіслати</button>
        </form>
    
        <form id="callback-form__verify" class="callback-form__verify hidden">
            <h2>Перевірка коду</h2>
            <input type="text" id="callback-form__verify-code" name="verify-code" placeholder="Введіть код з СМС">
            <button id="callback-form__verify-button">Перевірити</button>
            <span id="callback-form_messgage-status">Статус відправки повідомлення: <i>Надіслано</i>
        </form>
    
        <div id="callback-form__message"></div>
    `;
    
    // Set content or attributes if needed
    callbackFormElement.innerHTML = callbackFormTemplate;
    callbackFormElement.classList.add('callback-form__container');
    
    const body = document.body;
    body.appendChild(callbackFormElement); 
        
        
    function messageStatusChecker(delay=30000) {
        const checkerInterval = setInterval(() => {
            fetch('./callback-form/cf_verify_message_status_callback.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const status = document.querySelector("#callback-form_messgage-status i");
                    if(data.received) {
                        status.textContent('Доставлено');
                        clearInterval(checkerInterval);
                    }
                } else {
                    console.error('Помилка:', data.message);
                }
            })
            .catch(error => {
                console.error('Помилка:', error);
            });
        
        }, delay);                       
    }    
        
    const callbackForm = document.getElementById('callback-form');
    const verificationForm = document.getElementById('callback-form__verify');
    const messageDiv = document.getElementById('callback-form__message');
    const verifyButton = document.getElementById('callback-form__verify-button');
    const closeButton = document.getElementById('callback-form__close-button');  
    
    if (closeButton.length > 0) {
        // Event listener for callback form closing form
        closeButton.addEventListener('click', function(event) {
            event.preventDefault();
            // const form = document.getElementById('name').value;
    
            this.parentNode.style.display = "none";
        });   
    }
    if (callbackForm.length > 0) {
        // Event listener for callback form submission
        callbackForm.addEventListener('submit', function(event) {
            event.preventDefault();
    
            const phone = document.getElementById('callback-form__phone').value;
    
            const formData = new FormData();
            formData.append('phone', phone);
    
            fetch('./callback-form/cf_send_code_callback.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    callbackForm.classList.add('hidden');
                    verificationForm.classList.remove('hidden');
                    
                    messageStatusChecker()                    
                } else {
                    messageDiv.textContent = `Помилка: ${data.message}`;
                }
            })
            .catch(error => {
                console.error('Помилка:', error);
                messageDiv.textContent = 'An error occurred.';
            });
        });    
    }

    // Event listener for verification form submission
    verificationForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const verificationCode = document.getElementById('callback-form__verify-code').value;
        
        
        const formData = new FormData();
        formData.append('code', verificationCode);

        fetch('./callback-form/cf_verify_code_callback.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.textContent = 'Verification successful. Callback requested.';
            } else {
                messageDiv.textContent = `Error: ${data.message}`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.textContent = 'An error occurred.';
        });
    });
});
