// Main JavaScript para MicroHack

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar iconos de Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Auto-hide alerts después de 5 segundos
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Validación de formularios en tiempo real
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateInput(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('invalid')) {
                    validateInput(this);
                }
            });
        });
    });

    // Confirmación antes de salir si hay cambios sin guardar
    let formChanged = false;
    const formInputs = document.querySelectorAll('form input, form textarea, form select');
    
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            formChanged = true;
        });
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Resetear flag cuando se envía el formulario
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            formChanged = false;
        });
    });

    // Smooth scroll para enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Función de validación de inputs
function validateInput(input) {
    const value = input.value.trim();
    
    // Remover clases previas
    input.classList.remove('invalid', 'valid');
    
    // Validar si está vacío
    if (input.hasAttribute('required') && value === '') {
        input.classList.add('invalid');
        showInputError(input, 'Este campo es obligatorio');
        return false;
    }
    
    // Validar email
    if (input.type === 'email') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            input.classList.add('invalid');
            showInputError(input, 'Email inválido');
            return false;
        }
    }
    
    // Validar minlength
    if (input.hasAttribute('minlength')) {
        const minLength = parseInt(input.getAttribute('minlength'));
        if (value.length < minLength) {
            input.classList.add('invalid');
            showInputError(input, `Mínimo ${minLength} caracteres`);
            return false;
        }
    }
    
    // Validar pattern
    if (input.hasAttribute('pattern')) {
        const pattern = new RegExp(input.getAttribute('pattern'));
        if (!pattern.test(value)) {
            input.classList.add('invalid');
            showInputError(input, input.getAttribute('title') || 'Formato inválido');
            return false;
        }
    }
    
    // Si pasa todas las validaciones
    input.classList.add('valid');
    removeInputError(input);
    return true;
}

// Mostrar error en input
function showInputError(input, message) {
    removeInputError(input);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'input-error';
    errorDiv.style.cssText = `
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    `;
    errorDiv.textContent = message;
    
    input.parentNode.appendChild(errorDiv);
}

// Remover error de input
function removeInputError(input) {
    const error = input.parentNode.querySelector('.input-error');
    if (error) {
        error.remove();
    }
}

// Copiar al portapapeles
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Copiado al portapapeles', 'success');
        }).catch(err => {
            console.error('Error al copiar:', err);
            showNotification('Error al copiar', 'error');
        });
    } else {
        // Fallback para navegadores antiguos
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showNotification('Copiado al portapapeles', 'success');
        } catch (err) {
            console.error('Error al copiar:', err);
            showNotification('Error al copiar', 'error');
        }
        document.body.removeChild(textarea);
    }
}

// Mostrar notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        animation: slideInRight 0.3s ease-out;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Animaciones CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    input.invalid {
        border-color: #ef4444 !important;
    }
    
    input.valid {
        border-color: #10b981 !important;
    }
`;
document.head.appendChild(style);