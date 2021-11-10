panel.plugin('michnhokn/logger', {
  components: {
    'k-logger-view': {
      props: {logs: Array},
      template: `
  <k-inside>
    <k-view class="k-logger-view">
      <k-header>Logger</k-header>
      <pre>{{logs}}</pre>
    </k-view>
  </k-inside>`,
    },
  },
});
