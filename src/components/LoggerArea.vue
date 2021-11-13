<template>
  <k-inside>
    <k-view class="k-logger-view">
      <k-header>
        {{ $t('michnhokn.logger.title') }}
        <k-button-group slot="left">
          <k-button icon="refresh" @click="reset">{{ $t('michnhokn.logger.reset') }}</k-button>
        </k-button-group>
        <template slot="right">
          <k-pagination
              align="right"
              :details="true"
              :page="page"
              :limit="limit"
              :total="total"
              @paginate="paginate"/>
        </template>
      </k-header>

      <k-grid gutter="medium">
        <k-column width="2/6">
          <k-select-field v-model="filter.user"
                          :options="userOptions"
                          :label="$t('user')"
                          type="select"
                          icon="angle-down"/>
        </k-column>
        <k-column width="1/6">
          <k-date-field v-model="filter.timestamp"
                        display="YYYY-MM-DD"
                        :label="$t('date')"
                        :calendar="true"
                        type="date"/>
        </k-column>
        <k-column width="1/6">
          <k-select-field v-model="filter.type"
                          :options="typeOptions"
                          :label="$t('michnhokn.logger.type')"
                          type="select"
                          icon="angle-down"/>
        </k-column>
        <k-column width="1/6">
          <k-select-field v-model="filter.action"
                          :options="actionOptions"
                          :label="$t('michnhokn.logger.action')"
                          type="select"
                          icon="angle-down"/>
        </k-column>
        <k-column width="1/6">
          <k-select-field v-model="filter.language"
                          :options="languageOptions"
                          :label="$t('language')"
                          type="select"
                          icon="angle-down"/>
        </k-column>
        <k-column width="1/3">
          <k-text-field v-model="filter.slug"
                        type="text"
                        :label="$t('michnhokn.logger.slug')"
                        placeholder="Suche ..."
                        icon="search"/>
        </k-column>
        <k-column width="1/3">
          <k-text-field v-model="filter.oldSearch"
                        type="text"
                        :label="$t('michnhokn.logger.searchOld')"
                        :placeholder="$t('search')"
                        icon="search"/>
        </k-column>
        <k-column width="1/3">
          <k-text-field v-model="filter.newSearch"
                        type="text"
                        :label="$t('michnhokn.logger.searchNew')"
                        :placeholder="$t('search')"
                        icon="search"/>
        </k-column>
        <k-column width="1/1" style="overflow: auto">
          <table v-if="logs.length" class="k-system-plugins">
            <thead>
            <tr>
              <th style="width: 175px">{{ $t('date') }}</th>
              <th>{{ $t('user') }}</th>
              <th style="width: 70px">{{ $t('michnhokn.logger.type') }}</th>
              <th style="width: 140px">{{ $t('michnhokn.logger.action') }}</th>
              <th>{{ $t('michnhokn.logger.slug') }}</th>
              <th style="width: 90px">{{ $t('language') }}</th>
              <th>{{ $t('michnhokn.logger.old') }}</th>
              <th>{{ $t('michnhokn.logger.new') }}</th>
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
          <k-empty v-else icon="table" layout="cards">{{ $t('michnhokn.logger.empty') }}</k-empty>
        </k-column>
      </k-grid>
    </k-view>
  </k-inside>
</template>

<script>
export default {
  name: 'LoggerArea',
  props: ['userOptions', 'typeOptions', 'actionOptions', 'languageOptions'],
  data() {
    return {
      logs: [],
      total: 0,
      page: 1,
      limit: 10,
      filter: {
        timestamp: '',
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
        this.total = data.total;
      });
    },

    paginate({page, limit}) {
      this.page = page;
      this.limit = limit;
      this.fetch(page, limit);
    },

    reset() {
      this.filter = {
        timestamp: '',
        oldSearch: '',
        newSearch: '',
        action: '',
        type: '',
        user: '',
        slug: '',
        language: '',
      };
    },
  },
};
</script>

<style lang="scss" scoped>
table {
  width: 100%;
  border-collapse: collapse;
  display: block;

  @media screen and (min-width: 65em) {
    display: table;
  }

  td, th {
    border: 1px solid var(--color-border);
    overflow: auto;
    vertical-align: baseline;
    text-overflow: unset;
    white-space: normal;
  }
}
</style>