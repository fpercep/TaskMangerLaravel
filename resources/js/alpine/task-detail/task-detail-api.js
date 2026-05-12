/**
 * Task Detail — API Layer
 *
 * Funciones async para las operaciones CRUD del modal de detalle.
 * Cada función sigue el patrón optimista: mutación inmediata → axios → rollback en error.
 * Reciben el contexto mínimo necesario (rutas, datos) y devuelven { ok, error? }.
 */

/**
 * Guarda un campo individual de la tarea (PATCH genérico).
 * @param {string} updateRoute - Ruta con `:id` como placeholder.
 * @param {number} taskId
 * @param {string} field - Nombre del campo.
 * @param {*} value - Nuevo valor.
 * @returns {Promise<{ok: boolean}>}
 */
export async function saveField(updateRoute, taskId, field, value) {
    try {
        const url = updateRoute.replace(':id', taskId);
        await window.axios.patch(url, { [field]: value });
        return { ok: true };
    } catch (error) {
        console.error(`Error actualizando ${field}:`, error);
        return { ok: false };
    }
}

/**
 * Guarda múltiples campos en un solo PATCH.
 * @param {string} updateRoute
 * @param {number} taskId
 * @param {Object} payload - { field: value, ... }
 * @returns {Promise<{ok: boolean}>}
 */
export async function saveFields(updateRoute, taskId, payload) {
    try {
        const url = updateRoute.replace(':id', taskId);
        await window.axios.patch(url, payload);
        return { ok: true };
    } catch (error) {
        console.error('Error actualizando campos:', error);
        return { ok: false };
    }
}

// --- Steps API ---

/**
 * Crea un nuevo paso para la tarea.
 * @param {string} storeStepRoute
 * @param {number} taskId
 * @param {string} name
 * @returns {Promise<{ok: boolean, step?: Object}>}
 */
export async function createStep(storeStepRoute, taskId, name) {
    try {
        const url = storeStepRoute.replace(':id', taskId);
        const { data } = await window.axios.post(url, { name });
        return { ok: true, step: data.step };
    } catch (error) {
        console.error('Error creando paso:', error);
        return { ok: false };
    }
}

/**
 * Actualiza el nombre de un paso.
 * @param {string} updateStepRoute
 * @param {number} stepId
 * @param {string} name
 * @returns {Promise<{ok: boolean}>}
 */
export async function updateStepName(updateStepRoute, stepId, name) {
    try {
        const url = updateStepRoute.replace(':id', stepId);
        await window.axios.patch(url, { name });
        return { ok: true };
    } catch (error) {
        console.error('Error actualizando paso:', error);
        return { ok: false };
    }
}

/**
 * Alterna el estado completado de un paso.
 * @param {string} toggleStepRoute
 * @param {number} stepId
 * @returns {Promise<{ok: boolean}>}
 */
export async function toggleStepCompleted(toggleStepRoute, stepId) {
    try {
        const url = toggleStepRoute.replace(':id', stepId);
        await window.axios.patch(url);
        return { ok: true };
    } catch (error) {
        console.error('Error toggling paso:', error);
        return { ok: false };
    }
}

/**
 * Elimina un paso.
 * @param {string} deleteStepRoute
 * @param {number} stepId
 * @returns {Promise<{ok: boolean}>}
 */
export async function destroyStep(deleteStepRoute, stepId) {
    try {
        const url = deleteStepRoute.replace(':id', stepId);
        await window.axios.delete(url);
        return { ok: true };
    } catch (error) {
        console.error('Error eliminando paso:', error);
        return { ok: false };
    }
}
