<template>
  <v-card width="251">
    <v-list>
      <v-subheader class="white--text primary">
        クイズ正解数ランキング
      </v-subheader>
      <div class="text-right primary text-subtitle-2 pb-2">
        <a
          class=" white--text mr-2"
          :class="{ 'font-weight-bold': isActive.all }"
          @click="showRankingAll"
          >すべて</a
        >
        <a
          class=" white--text mr-2"
          :class="{ 'font-weight-bold': isActive.weekly }"
          @click="showRankingWeekly"
          >週間</a
        >
        <a
          class=" white--text mr-3"
          :class="{ 'font-weight-bold': isActive.monthly }"
          @click="showRankingMonthly"
          >月間</a
        >
      </div>
      <v-divider></v-divider>
      <v-list-item v-for="user in users_corrent" :key="user.id">
        <v-list-item-content>
          <v-row no-gutters>
            <v-col cols="2">
              {{ user.ranking }}
            </v-col>
            <v-col cols="8">
              <a class="user-name" @click.stop.prevent="goUserPage(user.id)">
                {{ user.name }}
              </a>
            </v-col>
            <v-col cols="2" class="text-right">
              {{ user.total_correct_count }}
            </v-col>
          </v-row>
        </v-list-item-content>
      </v-list-item>
    </v-list>
    <v-card-text class="mt-n3">
      <a @click.stop.prevent="goRankingCorrectPage()">
        ランキング一覧ページへ
      </a>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  props: {
    users: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      users_weekly: null,
      users_monthly: null,
      users_corrent: this.users,
      isActive: {
        all: true,
        weekly: false,
        monthly: false,
      },
    }
  },
  methods: {
    goUserPage(user_id) {
      this.$inertia.get(route('dashboard', { user: user_id }))
    },
    goRankingCorrectPage() {
      this.$inertia.get(route('ranking.correct'))
    },
    showRankingAll() {
      this.isActiveChange('all')
      this.users_corrent = this.users
    },
    async showRankingWeekly() {
      this.isActiveChange('weekly')
      if (this.users_weekly !== null) {
        this.users_corrent = this.users_weekly
        return
      }
      await axios
        .get(route('ranking.weekly'))
        .then(response => {
          this.users_weekly = response.data
          this.users_corrent = this.users_weekly
        })
        // 「Uncaught (in promise) Error: Request aborted」の抑制のため、追加
        .catch(() => {})
    },
    async showRankingMonthly() {
      this.isActiveChange('monthly')
      if (this.users_monthly !== null) {
        this.users_corrent = this.users_monthly
        return
      }
      await axios
        .get(route('ranking.monthly'))
        .then(response => {
          this.users_monthly = response.data
          this.users_corrent = this.users_monthly
        })
        // 「Uncaught (in promise) Error: Request aborted」の抑制のため、追加
        .catch(() => {})
    },
    isActiveChange(key) {
      if (key == 'all') {
        this.isActive.all = true
        this.isActive.weekly = false
        this.isActive.monthly = false
      } else if (key == 'weekly') {
        this.isActive.all = false
        this.isActive.weekly = true
        this.isActive.monthly = false
      } else if (key == 'monthly') {
        this.isActive.all = false
        this.isActive.weekly = false
        this.isActive.monthly = true
      }
    },
  },
}
</script>

<style>
a.user-name {
  color: black;
}
a.user-name:hover {
  text-decoration: underline;
}
</style>
