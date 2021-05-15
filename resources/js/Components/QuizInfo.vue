<template>
  <v-card 
    :max-width="maxWidth"
    class="mb-5 user-select-text"
    :elevation="elevation"
    @click.prevent="onClick"
    :disabled="detail"
  >
    <v-card-text>
      <v-row no-gutters>
        <v-col cols="8" sm="7" class="mb-5">
          <v-avatar :size="avatarSize">
            <img :src="quiz.user.profile_photo_url" :alt="quiz.user.name">
          </v-avatar>
          <span :class="{ 'text-h6': detail }">{{ quiz.user.name }}</span>
        </v-col>

        <v-col cols="8" sm="5" class="text-sm-right">
          <span class="mr-4">
            <v-icon color="blue">{{ mdiClipboardPlay }}</v-icon>
            {{ quiz.gradesCount }}
          </span>
          <span class="mr-1">
            <v-icon color="green">{{ mdiCommentTextMultiple }}</v-icon>
            {{ quiz.commentsCount }}
          </span>
          <v-btn class="mr-1 pa-0" text>
            <v-icon color="red">{{ mdiHeart }}</v-icon>
            2
          </v-btn>
        </v-col>

        <v-col cols="4" class="mt-n10 mt-sm-0">
          <v-img :src="quiz.url" :width="imgWidth"></v-img>
        </v-col>

        <v-col cols="12" sm="8" class="ml-sm-5 mr-sm-n5" align-self="center">
          <table :class="{ 'text-subtitle-1' : detail }">
            <tr>
              <th width="80px" class="text-left">タイトル</th>
              <td class="pa-1">{{ quiz.title }}</td>
            </tr>
            <tr>
              <th class="text-left">説明</th>
              <td class="pa-1">{{ quiz.description }}</td>
            </tr>
            <tr>
              <th class="text-left">ジャンル</th>
              <td class="pa-1">{{ quiz.genre.name }}</td>
            </tr>
            <tr>
              <th class="text-left">作成日</th>
              <td class="pa-1">{{ quiz.formattedCreatedAt }}</td>
            </tr>
          </table>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import { mdiClipboardPlay, mdiCommentTextMultiple, mdiHeart } from '@mdi/js';

export default {
  props: {
    quiz: {
      type: Object,
      required: true,
    },
    detail: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      mdiClipboardPlay, mdiCommentTextMultiple, mdiHeart,
      elevation: undefined,
      maxWidth: 800,
      avatarSize: 35,
      imgWidth: 200,
    }
  },
  mounted() {
    if (this.detail) {
      this.elevation = 0
      this.maxWidth = undefined
      this.avatarSize = 55
      this.imgWidth = 300
    }
  },
  methods: {
    onClick() {
      this.$inertia.get(route('quiz.detail', {
        quiz: this.quiz.id,
      }))
    }
  },
}
</script>

<style scoped>
.user-select-text {
  user-select: text;
}
</style>