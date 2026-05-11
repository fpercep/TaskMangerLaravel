/**
 * Inicializa los listeners de Laravel Echo para eventos globales.
 * 
 * @param {number|null} userId ID del usuario autenticado.
 */
export default function echoListeners(userId) {
    if (!userId || !window.Echo) {
        console.warn('Echo o UserId no disponibles para inicializar listeners.');
        return;
    }

    // Escuchar en el canal privado del usuario
    window.Echo.private(`App.Models.User.${userId}`)
        .listen('MemberRemovedFromProject', (payload) => {
            console.log('Evento recibido: MemberRemovedFromProject', payload);
            window.dispatchEvent(
                new CustomEvent('project-removed', { detail: payload })
            );
        })
        .listen('MemberAddedToProject', (payload) => {
            console.log('Evento recibido: MemberAddedToProject', payload);
            window.dispatchEvent(
                new CustomEvent('project-added', { detail: payload })
            );
        })
        .listen('ProjectDetailsUpdated', (payload) => {
            console.log('Evento recibido: ProjectDetailsUpdated', payload);
            window.dispatchEvent(
                new CustomEvent('project-updated', { detail: payload })
            );
        });
}
