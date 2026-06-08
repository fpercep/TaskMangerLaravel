export default (initialTasks) => ({
    tasks: initialTasks,
    search: '',
    filter: 'all',
    
    get filteredTasks() {
        return this.tasks.filter(task => {
            const term = this.search.toLowerCase();
            const matchesSearch = task.name.toLowerCase().includes(term) || 
                                  (task.project && task.project.name.toLowerCase().includes(term));
            
            const matchesFilter = this.filter === 'all' || task.status === this.filter;
            
            return matchesSearch && matchesFilter;
        });
    },

    goToTask(task) {
        if (task.project && task.project.id) {
            window.location.href = `/projects/${task.project.id}?task=${task.id}`;
        }
    },

    handleRealtimeUpdate(updatedTask) {
        const isAssignedToMe = updatedTask.assigned_user_id === window.AppUserId;
        const isActive = updatedTask.status !== 'completed' && updatedTask.status !== 'cancelled';
        const index = this.tasks.findIndex(t => t.id === updatedTask.id);

        if (isAssignedToMe && isActive) {
            const mappedTask = {
                id: updatedTask.id,
                name: updatedTask.name,
                status: updatedTask.status,
                priority: updatedTask.priority,
                due_date: updatedTask.due_date,
                project: updatedTask.project ? {
                    id: updatedTask.project.id,
                    name: updatedTask.project.name,
                } : null
            };
            if (index !== -1) {
                this.tasks[index] = mappedTask;
            } else {
                this.tasks.push(mappedTask);
            }
        } else {
            if (index !== -1) {
                this.tasks.splice(index, 1);
            }
        }
    },

    handleRealtimeDelete(taskId) {
        const index = this.tasks.findIndex(t => t.id === taskId);
        if (index !== -1) {
            this.tasks.splice(index, 1);
        }
    },
    
    statusLabel(status) {
        const labels = {
            'pending': 'Pendiente',
            'in_progress': 'En Progreso',
            'completed': 'Completada',
            'cancelled': 'Cancelada'
        };
        return labels[status] || status;
    },
    
    priorityLabel(priority) {
        const labels = {
            'urgent': 'Urgente',
            'high': 'Alta',
            'medium': 'Media',
            'low': 'Baja'
        };
        return labels[priority] || priority;
    },

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
    },

    isOverdue(task) {
        if (!task.due_date || task.status === 'completed' || task.status === 'cancelled') return false;
        
        const dueDate = new Date(task.due_date);
        dueDate.setHours(0,0,0,0);
        
        const today = new Date();
        today.setHours(0,0,0,0);
        
        return dueDate < today;
    }
});
