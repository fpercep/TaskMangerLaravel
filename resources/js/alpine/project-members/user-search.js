export default function userSearch() {
    return {
        query: '',
        results: [],
        isSearching: false,
        showPanel: false,
        clearTimeout: null,

        init() {
            this.$watch('query', () => {
                if (this.timeout) clearTimeout(this.timeout);
                this.timeout = setTimeout(() => this.search(), 300);
            });

            this.$watch('showPanel', value => {
                if (!value) {
                    this.clearTimeout = setTimeout(() => {
                        this.query = '';
                        this.results = [];
                    }, 10000);
                } else if (this.clearTimeout) {
                    clearTimeout(this.clearTimeout);
                }
            });
        },

        async search() {
            const query = this.query.trim();
            if (query.length < 2) {
                this.results = [];
                return;
            }

            this.isSearching = true;
            try {
                const response = await fetch(`/users/search?search=${encodeURIComponent(query)}`);
                const { data } = await response.json();
                this.results = data;
            } catch (error) {
                console.error('Error searching users:', error);
            } finally {
                this.isSearching = false;
            }
        },

        selectUser(userId) {
            this.$dispatch('user-selected', { userId });
            this.showPanel = false;
        }
    };
}
