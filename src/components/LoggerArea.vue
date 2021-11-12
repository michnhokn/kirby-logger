<template>
  <k-inside>
    <k-view class="k-logger-view">
      <k-header>Logger</k-header>
      <table v-if="logs" class="k-system-plugins">
        <thead>
        <tr>
          <th>Timestamp</th>
          <th>
            <k-input v-model="filter.user"
                     :options="userOptions"
                     :class="{selected:filter.user!==''}"
                     type="select"
                     placeholder="User"
                     icon="angle-down"/>
          </th>
          <th>
            <k-input v-model="filter.type"
                     :options="typeOptions"
                     :class="{selected:filter.type!==''}"
                     type="select"
                     placeholder="Type"
                     icon="angle-down"/>
          </th>
          <th>
            <k-input v-model="filter.action"
                     :options="actionOptions"
                     :class="{selected:filter.action!==''}"
                     type="select"
                     placeholder="Action"
                     icon="angle-down"/>
          </th>
          <th>
            <k-input v-model="filter.slug"
                     :options="slugOptions"
                     :class="{selected:filter.slug!==''}"
                     type="select"
                     placeholder="Slug"
                     icon="angle-down"/>
          </th>
          <th>
            <k-input v-model="filter.language"
                     :options="languageOptions"
                     :class="{selected:filter.language!==''}"
                     type="select"
                     placeholder="Language"
                     icon="angle-down"/>
          </th>
          <th>
            <k-input v-model="filter.oldSearch"
                     :class="{selected:filter.oldSearch!==''}"
                     type="text"
                     placeholder="Suche ..."
                     icon="search"/>
          </th>
          <th>
            <k-input v-model="filter.newSearch"
                     :class="{selected:filter.newSearch!==''}"
                     type="text"
                     placeholder="Suche ..."
                     icon="search"/>
          </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="log in logs" :key="log.id">
          <td>{{ log.timestamp }}</td>
          <td>{{ log.user }}</td>
          <td>{{ log.type }}</td>
          <td>{{ log.action }}</td>
          <td>{{ log.slug }}</td>
          <td>{{ log.language }}</td>
          <td>{{ log.oldData }}</td>
          <td>{{ log.newData }}</td>
        </tr>
        </tbody>
      </table>
      <k-pagination
          align="right"
          :details="true"
          :keys="true"
          :page="page"
          :limit="limit"
          :total="total"
          @paginate="paginate"/>
    </k-view>
  </k-inside>
</template>

<script>
export default {
  name: 'LoggerArea',
  // TODO: Get filter optios as props
  data() {
    return {
      logs: [],
      total: 0,
      page: 1,
      limit: 15,
      filter: {
        oldSearch: '',
        newSearch: '',
        action: '',
        type: '',
        user: '',
        slug: '',
        language: '',
      },
    };
  },
  mounted() {
    this.fetch(this.page, this.limit);
  },
  watch: {
    filter: {
      handler(filter) {
        this.fetch(this.page, this.limit, filter);
      },
      deep: true,
    },
  },
  methods: {
    fetch(page = 1, limit = 10, filter = null) {
      return this.$api.post('logs.json', {page, limit, filter}).then((data) => {
        this.logs = data.logs;
        console.log(this.logs);
        this.total = data.total;
      });
    },

    paginate({page, limit}) {
      this.page = page;
      this.limit = limit;
      this.fetch(page, limit);
    },
  },
  computed: {
    userOptions() {
      const users = this.logs.map((log) => log.user);
      const uniqueUsers = [...new Set(users)];
      return uniqueUsers.map((user) => ({value: user, text: user}));
    },
    typeOptions() {
      const types = this.logs.map((log) => log.type);
      const uniqueTypes = [...new Set(types)];
      return uniqueTypes.map((type) => ({value: type, text: type}));
    },
    actionOptions() {
      const actions = this.logs.map((log) => log.action);
      const uniqueActions = [...new Set(actions)];
      return uniqueActions.map((action) => ({value: action, text: action}));
    },
    slugOptions() {
      const slugs = this.logs.map((log) => log.slug);
      const uniqueSlugs = [...new Set(slugs)];
      return uniqueSlugs.map((slug) => ({value: slug, text: slug}));
    },
    languageOptions() {
      const languages = this.logs.map((log) => log.language);
      const uniquelanguages = [...new Set(languages)];
      return uniquelanguages.map((language) => ({value: language, text: language}));
    },
  },
};
</script>

<style lang="scss">
table {
  width: 100%;
  border-collapse: collapse;

  td, th {
    padding: 6px 12px;
    text-align: left;
    border: 1px solid var(--color-border);
  }
}

.selected {
  font-weight: bold;
}
</style>