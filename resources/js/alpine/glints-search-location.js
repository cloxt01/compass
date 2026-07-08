export default () => ({
    query: '',
    results: [],
    searching: false,

    async search() {
        const q = this.query.trim();

        if (q.length < 2) {
            this.results = [];
            return;
        }

        this.searching = true;

        try {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.content : '';

            const response = await fetch('/api/glints/search-location', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    keyword: q,
                }),
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const json = await response.json();

            // console.log('GlintsHelper Response:', json);

            this.results = json.ok
                ? (json.data?.searchHierarchicalLocations ?? [])
                : [];

        } catch (error) {
            // console.error('Location search error:', error);
            this.results = [];
        } finally {
            this.searching = false;
        }
    },

    selectLocation(id, formattedName) {
        this.$wire.addLocation(id, formattedName);

        this.query = '';
        this.results = [];
    }
})
