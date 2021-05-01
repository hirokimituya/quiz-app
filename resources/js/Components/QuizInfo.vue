<template>
  <v-card max-width="800" class="mb-5">
    <v-card-text>
      <v-row no-gutters>
        <v-col cols="8" sm="7">
          <v-avatar size="35">
            <img :src="quiz.user.profile_photo_url" :alt="quiz.user.name">
          </v-avatar>
          {{ quiz.user.name }}
        </v-col>

        <v-col cols="8" sm="5" class="text-sm-right">
          <span class="mr-4">
            <v-icon color="blue">{{ mdiClipboardPlay }}</v-icon>
            5
          </span>
          <span class="mr-1">
            <v-icon color="green">{{ mdiCommentTextMultiple }}</v-icon>
            5
          </span>
          <v-btn class="mr-1 pa-0" text>
            <v-icon color="red">{{ mdiHeart }}</v-icon>
            2
          </v-btn>
        </v-col>

        <v-col cols="4" class="mt-n10 mt-sm-0">
          <v-img :src="quiz.url" width="200"></v-img>
        </v-col>

        <v-col cols="12" sm="8" class="mt-sm-5">
          <table>
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
    }
  },
  data() {
    return {
      mdiClipboardPlay, mdiCommentTextMultiple, mdiHeart,
    }
  }
}
</script>