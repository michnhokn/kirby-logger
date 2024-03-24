<template>
  <k-panel-inside class="k-logger-area">

    <k-header>
      Logs ({{ total }})
      <template #buttons>
        <k-button-group>
          <k-button icon="undo"
                    variant="filled"
                    @click="resetFilters">Zurücksetzten
          </k-button>
          <k-button icon="refresh"
                    variant="filled"
                    @click="fetchLogs">Neu laden
          </k-button>
        </k-button-group>
      </template>
    </k-header>

    <k-grid style="gap: 1rem; --columns: 3;margin-bottom: 2rem">
      <k-text-field
        name="search"
        label="Suche"
        icon="search"
        placeholder="Logs durchsuchen ..."
        :value="term"
        @input="onSearch"
      />

      <k-multiselect-field
        name="levels"
        label="Log Level"
        icon="filter"
        :options="levelOptions"
        :value="level"
        @input="onLevelChange"
      />

      <k-multiselect-field
        name="channel"
        label="Channel"
        icon="filter"
        :options="channelOptions"
        :value="channel"
        @input="onChannelChange"
      />
    </k-grid>

    <div class="k-table">
      <table>
        <thead>
        <tr>
          <th data-mobile="true" style="width: 12rem">Date</th>
          <th data-mobile="true" style="width: 8rem">Level</th>
          <th data-mobile="true" style="width: 10rem">Channel</th>
          <th>Message</th>
          <th>Context</th>
          <th data-mobile="true" class="k-table-options-column"></th>
        </tr>
        </thead>
        <tbody>
        <template v-if="logs.length > 0">
          <tr v-for="log in logs" :key="i">
            <td data-mobile="true">{{ $library.dayjs(log.created_at).format('DD.MM.YYYY HH:mm:ss') }}</td>
            <td data-mobile="true">
              <k-bubble :style="getLevelBackground(log.level)" :text="log.level"/>
            </td>
            <td data-mobile="true">{{ log.channel }}</td>
            <td>{{ log.message }}</td>
            <td>
              <div class="context">{{ log.context }}</div>
            </td>
            <td data-mobile="true" class="k-table-options-column">
              <k-button @click="onPreview(log)" icon="preview"/>
            </td>
          </tr>
        </template>
        <template v-else>
          <tr>
            <td class="k-table-empty" colspan="10">Keine Logs gefunden</td>
          </tr>
        </template>
        </tbody>
      </table>
      <k-pagination
        class="k-table-pagination"
        :details="true"
        :limit="limit"
        :page="page"
        :total="total"
        @paginate="fetchLogs"
      />
    </div>

  </k-panel-inside>
</template>

<script>
export default {
  props: {
    channels: [],
    levels: [],
  },

  data() {
    return {
      logs: [],
      term: null,
      level: null,
      channel: null,
      page: 1,
      limit: 50,
      total: 0,
    };
  },

  computed: {
    channelOptions() {
      return this.channels.map((channel) => {
        return {
          value: channel,
          text: channel,
        };
      });
    },

    levelOptions() {
      return this.levels.map((level) => {
        return {
          value: level,
          text: level,
        };
      });
    },
  },

  created() {
    this.fetchLogs();
  },

  methods: {
    onSearch(term) {
      if (term.length > 2) {
        this.term = term;
        this.fetchLogs();
        return;
      }

      this.term = null;
      this.fetchLogs();
    },

    async fetchLogs(pagination = null) {
      this.page = pagination?.page || this.page;
      this.limit = pagination?.limit || this.limit;

      const response = await window.panel.api.post('logs.json', {
        page: this.page,
        limit: this.limit,
        level: this.level,
        channel: this.channel,
        term: this.term,
      });

      this.logs = response.logs;
      this.total = response.total;
    },

    resetFilters() {
      this.page = 1;
      this.level = [];
      this.channel = [];
      this.term = null;
      this.fetchLogs();
    },

    onLevelChange(level) {
      this.level = level;
      this.fetchLogs();
    },

    onChannelChange(channel) {
      this.channel = channel;
      this.fetchLogs();
    },

    onPreview(log) {
      window.panel.drawer.open({
        component: 'k-logger-drawer',
        props: {
          icon: 'preview',
          title: `[${log.level}] ${this.$library.dayjs(log.datetime).
            format('DD.MM.YYYY HH:mm:ss')}: ${log.channel}`,
          text: log,
          options: [
            {
              icon: 'cancel',
              title: 'Schließen',
              click: () => window.panel.drawer.close(),
            }],
        },
      });
    },

    getLevelBackground(levelName) {
      switch (levelName) {
        case 'DEBUG':
          return {backgroundColor: 'var(--color-gray-400'};
        case 'INFO':
          return {backgroundColor: 'var(--color-blue-400'};
        case 'WARNING':
          return {backgroundColor: 'var(--color-yellow-400'};
        case 'ERROR':
          return {backgroundColor: 'var(--color-red-400'};
        case 'CRITICAL':
          return {backgroundColor: 'var(--color-pink-400'};
        default:
          return {backgroundColor: 'transparent'};
      }
    },
  },
};
</script>

<style scoped>
.context {
  height: calc(14px * 1.5);
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
</style>
