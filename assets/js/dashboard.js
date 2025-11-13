// Dashboard JavaScript para MicroHack

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar iconos
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Mobile sidebar toggle
    initMobileSidebar();

    // Auto-refresh de workspaces cada 30 segundos
    if (window.location.pathname.includes('flash.php')) {
        setInterval(refreshWorkspaces, 30000);
    }
});

// Inicializar sidebar móvil
function initMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    
    if (window.innerWidth <= 1024 && sidebar) {
        // Crear botón de menú
        const menuButton = document.createElement('button');
        menuButton.className = 'mobile-menu-btn';
        menuButton.innerHTML = '<i data-lucide="menu"></i>';
        menuButton.style.cssText = `
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 100;
            background: white;
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        `;
        
        document.body.appendChild(menuButton);
        
        menuButton.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
        
        // Cerrar sidebar al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !menuButton.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
        
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
}

// Iniciar workspace
function startWorkspace(workspaceId) {
    if (!confirm('¿Iniciar este workspace?')) return;
    
    showLoading();
    
    fetch('../api/workspace-actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'start',
            workspace_id: workspaceId
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showNotification('Workspace iniciado correctamente', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('Error al iniciar workspace', 'error');
    });
}

// Detener workspace
function stopWorkspace(workspaceId) {
    if (!confirm('¿Detener este workspace?')) return;
    
    showLoading();
    
    fetch('../api/workspace-actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'stop',
            workspace_id: workspaceId
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showNotification('Workspace detenido correctamente', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('Error al detener workspace', 'error');
    });
}

// Eliminar workspace
function deleteWorkspace(workspaceId) {
    if (!confirm('¿Estás seguro de eliminar este workspace? Esta acción no se puede deshacer.')) return;
    
    showLoading();
    
    fetch('../api/workspace-actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'delete',
            workspace_id: workspaceId
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showNotification('Workspace eliminado correctamente', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('Error al eliminar workspace', 'error');
    });
}

// Editar workspace
function editWorkspace(workspaceId) {
    window.location.href = `create-workspace.php?edit=${workspaceId}`;
}

// Resetear workspace
function resetWorkspace(workspaceId) {
    if (!confirm('¿Resetear este workspace? Se restaurarán todos los archivos a su estado original.')) return;
    
    showLoading();
    
    fetch('../api/workspace-actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'reset',
            workspace_id: workspaceId
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showNotification('Workspace reseteado correctamente', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('Error al resetear workspace', 'error');
    });
}

// Refrescar lista de workspaces
function refreshWorkspaces() {
    fetch('../api/workspace-manager.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'list'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateWorkspacesList(data.workspaces);
        }
    })
    .catch(error => {
        console.error('Error al refrescar workspaces:', error);
    });
}

// Actualizar lista de workspaces en el DOM
function updateWorkspacesList(workspaces) {
    // Implementar lógica para actualizar la UI sin recargar la página
    console.log('Workspaces actualizados:', workspaces);
}

// Mostrar loading
function showLoading() {
    const loading = document.createElement('div');
    loading.id = 'loading-overlay';
    loading.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    `;
    
    loading.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(loading);
}

// Ocultar loading
function hideLoading() {
    const loading = document.getElementById('loading-overlay');
    if (loading) {
        loading.remove();
    }
}

// Copiar al portapapeles
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('URL copiada al portapapeles', 'success');
        }).catch(err => {
            console.error('Error al copiar:', err);
            showNotification('Error al copiar', 'error');
        });
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
        font-family: 'Product Sans', sans-serif;
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