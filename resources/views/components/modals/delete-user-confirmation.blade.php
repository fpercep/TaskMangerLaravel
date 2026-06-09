<x-ui.dialog name="delete-user" max-width="sm" centered alpine-data="{
    user: null,
    deleteUrl() {
        return '{{ url('admin/users') }}/' + (this.user?.id || '');
    }
}">
    <div class="p-6 text-center">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50">
            <x-lucide-alert-triangle class="h-6 w-6 text-red-500" />
        </div>

        <h3 class="text-base font-semibold text-gray-900 mb-1">Eliminar usuario</h3>
        <p class="text-sm text-gray-500 mb-5">
            ¿Estás seguro de que quieres eliminar a <strong x-text="user?.name" class="text-gray-700"></strong>?
            Esta acción no se puede deshacer.
        </p>

        <div class="flex items-center gap-3 justify-center">
            <button type="button" @click="show = false" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-colors focus:outline-none">
                Cancelar
            </button>
            <form :action="deleteUrl()" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-5 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600">
                    Eliminar
                </button>
            </form>
        </div>
    </div>
</x-ui.dialog>
