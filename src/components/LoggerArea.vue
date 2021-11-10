<template>
  <k-inside>
    <k-view class="k-logger-view">
      <k-header>Logger</k-header>
      <k-text-field v-model="searchTerm" name="text" placeholder="Suche" icon="search"/>
      <br>
      <table class="k-system-plugins">
        <thead>
        <tr>
          <th>Timestamp</th>
          <th>User</th>
          <th>Type</th>
          <th>Action</th>
          <th>Old</th>
          <th>New</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="log in entries" :key="log.id">
          <td>{{ log.timestamp || log.item.timestamp }}</td>
          <td>{{ log.user || log.item.user }}</td>
          <td>{{ log.type || log.item.type }}</td>
          <td>{{ log.action || log.item.action }}</td>
          <td>{{ log.old || log.item.old }}</td>
          <td>{{ log.new || log.item.new }}</td>
        </tr>
        </tbody>
      </table>

    </k-view>
  </k-inside>
</template>

<script>
import Fuse from 'fuse.js';
import {JSONView} from 'vue-json-component/dist/index.umd';

export default {
  name: 'LoggerArea',
  props: ['logs'],
  components: {'json-view': JSONView},
  data() {
    return {
      searchTerm: '',
      currentArguments: null,
      fuse: null,
    };
  },
  computed: {
    entries() {
      return this.searchTerm ? this.fuse?.search(this.searchTerm) : this.logs?.data;
    },
  },
  mounted() {
    this.fuse = new Fuse(this.logs?.data, {
      includeScore: true,
      keys: ['action', 'type', 'email', 'timestamp'],
    });
  },
  methods: {
    showArguments(args) {
      this.currentArguments = args;
      this.$refs.dialog.open();
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
</style>