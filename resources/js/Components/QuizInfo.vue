<template>
  <v-card
    :max-width="maxWidth"
    class="mb-5"
    :elevation="elevation"
    @[eventName]="onClick"
  >
    <v-card-text>
      <v-row no-gutters>
        <v-col cols="8" sm="7" class="mb-5">
          <a text-decoration="none" @click.stop.prevent="mypage">
            <v-avatar :size="avatarSize">
              <img :src="quiz.user.profile_photo_url" :alt="quiz.user.name" />
            </v-avatar>
          </a>
          <a
            class="black--text"
            text-decoration="none"
            @click.stop.prevent="mypage"
          >
            <span :class="{ 'text-h6': detail }">{{ quiz.user.name }}</span>
          </a>
        </v-col>

        <v-col cols="8" sm="5" class="text-sm-right mb-2">
          <div class="mb-2">
            <span class="mr-4">
              <v-icon color="blue">{{ mdiClipboardPlay }}</v-icon>
              {{ quiz.gradesCount }}
            </span>
            <span class="mr-1">
              <v-icon color="green">{{ mdiCommentTextMultiple }}</v-icon>
              {{ quiz.commentsCount }}
            </span>
            <v-btn
              class="mr-1 pa-0"
              text
              :disabled="likeDisabled"
              @click.stop.prevent="like"
            >
              <v-icon :color="likedByUser ? 'red' : 'grey'">{{
                likedByUser ? mdiHeart : mdiHeartOutline
              }}</v-icon>
              {{ likesCount }}
            </v-btn>
          </div>
          <span class="mr-5"> 平均正解率：{{ avgCorrectRate }}% </span>
        </v-col>

        <v-col cols="4" class="mt-n10 mt-sm-0">
          <v-img :src="quiz.url" :width="imgWidth"></v-img>
        </v-col>

        <v-col cols="12" sm="8" class="ml-sm-5 mr-sm-n5" align-self="center">
          <table :class="{ 'text-subtitle-1': detail }">
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
import {
  mdiClipboardPlay,
  mdiCommentTextMultiple,
  mdiHeart,
  mdiHeartOutline,
} from '@mdi/js'
import { putDesimalPointTwo } from '@/util'

export default {
  props: {
    quiz: {
      type: Object,
      required: true,
    },
    detail: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      mdiClipboardPlay,
      mdiCommentTextMultiple,
      mdiHeart,
      mdiHeartOutline,
      elevation: undefined,
      maxWidth: 800,
      avatarSize: 35,
      imgWidth: 200,
      likesCount: this.quiz.likesCount,
      likedByUser: this.quiz.likedByUser,
      likeDisabled: false,
    }
  },
  computed: {
    eventName() {
      return this.detail ? null : 'click'
    },
    avgCorrectRate() {
      return putDesimalPointTwo(this.quiz.avgCorrectRate)
    },
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
      this.$inertia.get(
        route('quiz.detail', {
          quiz: this.quiz.id,
        }),
      )
    },
    async like() {
      this.likeDisabled = true

      if (this.$page.props.user === null) {
        this.$inertia.get(
          route('login'),
          {},
          {
            headers: {
              'X-Login-Referer': true,
            },
          },
        )
      } else if (!this.likedByUser) {
        await axios
          .put(
            route('quiz.like', {
              quiz: this.quiz.id,
            }),
          )
          .then(() => {
            this.likesCount++
          })
          // 「Uncaught (in promise) Error: Request aborted」の抑制のため、追加
          .catch(() => {})

        this.likedByUser = true
      } else {
        await axios
          .delete(
            route('quiz.like', {
              quiz: this.quiz.id,
            }),
          )
          .then(() => {
            this.likesCount--
          })
          // 「Uncaught (in promise) Error: Request aborted」の抑制のため、追加
          .catch(() => {})

        this.likedByUser = false
      }

      this.likeDisabled = false
    },
    mypage() {
      this.$inertia.get(
        route('dashboard', {
          user: this.quiz.user.id,
        }),
      )
    },
  },
}
</script>
