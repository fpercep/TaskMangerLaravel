<x-ui.dialog name="save-user" max-width="md" alpine-data="{
    user: null,
    currentUserId: null,
    form: { name: '', email: '', password: '', is_super_admin: false },

    isEditing() {
        return this.user !== null && this.user && this.user.id;
    },

    isSelf() {
        return this.isEditing() && this.user.id === this.currentUserId;
    },

    onOpen() {
        if (this.user) {
            this.form.name = this.user.name || '';
            this.form.email = this.user.email || '';
            this.form.password = '';
            this.form.is_super_admin = !!this.user.is_super_admin;
        } else {
            this.form = { name: '', email: '', password: '', is_super_admin: false };
        }
        this.$nextTick(() => this.$refs.userName?.focus());
    },

    actionUrl() {
        if (this.isEditing()) {
            return '{{ url('admin/users') }}/' + this.user.id;
        }
        return '{{ route('admin.users.store') }}';
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-800" x-text="isEditing() ? 'Editar usuario' : 'Crear nuevo usuario'"></h3>
        <button @click="show = false" type="button" class="text-gray-400 hover:text-gray-700 hover:bg-gray-50 p-1.5 rounded-btn transition-colors focus:outline-none">
            <x-lucide-x class="size-icon-sm" />
        </button>
    </div>

    <!-- Formulario -->
    <form :action="actionUrl()" method="POST" class="p-5">
        @csrf
        <input type="hidden" name="_method" :value="isEditing() ? 'PUT' : 'POST'">

        <div class="mb-4">
            <x-ui.input-label for="user_name" value="Nombre" />
            <x-ui.text-input id="user_name" name="name" x-model="form.name" x-ref="userName" required placeholder="Nombre completo" />
            @error('name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <x-ui.input-label for="user_email" value="Correo electrónico" />
            <x-ui.text-input id="user_email" name="email" type="email" x-model="form.email" required placeholder="usuario@ejemplo.com" />
            @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <x-ui.input-label for="user_password">
                Contraseña
                <span x-show="isEditing()" class="text-gray-300 font-normal normal-case">(Dejar vacío para no cambiar)</span>
            </x-ui.input-label>
            <x-ui.text-input id="user_password" name="password" type="password" x-model="form.password" x-bind:required="!isEditing()" placeholder="Mínimo 8 caracteres" />
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5 flex items-center gap-3">
            <label for="user_is_super_admin" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Administrador</label>
            <button 
                type="button" 
                @click="if (!isSelf()) form.is_super_admin = !form.is_super_admin" 
                :class="[
                    form.is_super_admin ? 'bg-orange-500' : 'bg-gray-200',
                    isSelf() ? 'opacity-50 cursor-not-allowed' : ''
                ]" 
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2" 
                role="switch"
                :aria-checked="form.is_super_admin"
                :disabled="isSelf()">
                <span 
                    :class="form.is_super_admin ? 'translate-x-5' : 'translate-x-0'" 
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                </span>
            </button>
            <span x-show="isSelf()" class="text-xs text-gray-400 italic">No puedes cambiar tu propio rol</span>
            <input type="hidden" name="is_super_admin" :value="form.is_super_admin ? '1' : '0'">
        </div>

        <!-- Botonera -->
        <div class="flex items-center gap-3 justify-center pt-2">
            <button type="button" @click="show = false" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-colors focus:outline-none">
                Cancelar
            </button>
            <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium py-2 px-6 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                <span x-text="isEditing() ? 'Guardar Cambios' : 'Crear Usuario'"></span>
            </button>
        </div>
    </form>
</x-ui.dialog>
