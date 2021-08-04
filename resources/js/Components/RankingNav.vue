<template>
  <v-card width="251">
    <v-list>
      <v-subheader class="white--text primary">
        クイズ正解数ランキング
      </v-subheader>
      <v-divider></v-divider>
      <v-list-item v-for="user in users" :key="user.id">
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
  methods: {
    goUserPage(user_id) {
      this.$inertia.get(route('dashboard', { user: user_id }))
    },
    goRankingCorrectPage() {
      this.$inertia.get(route('ranking.correct'))
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
