<template>
  <v-card elevation="0" class="mb-7">
    <v-row no-gutters>
      <v-col cols="2" sm="1" class="text-center">
        <a text-decoration="none" @click.stop.prevent="mypage">
          <v-avatar :size="45">
            <img
              :src="comment.author.profile_photo_url"
              :alt="comment.author.name"
            />
          </v-avatar>
        </a>
      </v-col>

      <v-col cols="9" sm="10">
        <v-row no-gutters>
          <v-col>
            <a
              class="black--text"
              text-decoration="none"
              @click.stop.prevent="mypage"
            >
              <span>{{ comment.author.name }}</span>
            </a>
          </v-col>
        </v-row>

        <v-row no-gutters class="mt-2">
          <v-col>
            <span class="text-subtitle-1">{{ comment.content }}</span>
          </v-col>
        </v-row>
      </v-col>

      <v-col
        cols="1"
        align-self="center"
        class="text-right"
        v-if="editAndDeleteButtonDisp"
      >
        <v-menu offset-y>
          <template #activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>{{ mdiDotsVertical }}</v-icon>
            </v-btn>
          </template>
          <v-list min-width="120">
            <v-list-item @click="edit_dialog_flg = true">
              <v-list-item-title>
                <v-icon>{{ mdiPencil }}</v-icon>
                編集
              </v-list-item-title>
            </v-list-item>
            <v-list-item @click="delete_dialog_flg = true">
              <v-list-item-title>
                <v-icon>{{ mdiDelete }}</v-icon>
                削除
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>

        <!-- コメント編集ダイアログ -->
        <comment-edit-dialog
          v-model="edit_dialog_flg"
          :quiz-id="quizId"
          :comment="comment"
        ></comment-edit-dialog>

        <!-- コメント削除ダイアログ -->
        <comment-delete-dialog
          v-model="delete_dialog_flg"
          :quiz-id="quizId"
          :comment="comment"
        ></comment-delete-dialog>
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
import CommentEditDialog from '@/Components/CommentEditDialog'
import CommentDeleteDialog from '@/Components/CommentDeleteDialog'

import { mdiDotsVertical, mdiPencil, mdiDelete } from '@mdi/js'

export default {
  components: {
    CommentEditDialog,
    CommentDeleteDialog,
  },
  props: {
    quizId: {
      type: Number,
      required: true,
    },
    comment: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      mdiDotsVertical,
      mdiPencil,
      mdiDelete,
      edit_dialog_flg: false,
      delete_dialog_flg: false,
    }
  },
  computed: {
    editAndDeleteButtonDisp() {
      if (this.$page.props.user == null) {
        return false
      } else {
        return this.$page.props.user.id == this.comment.author.id
      }
    },
  },
  methods: {
    mypage() {
      this.$inertia.get(
        route('dashboard', {
          user: this.comment.author.id,
        }),
      )
    },
  },
}
</script>
