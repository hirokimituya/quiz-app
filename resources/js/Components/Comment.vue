<template>
  <v-card elevation="0" class="mb-7">
    <v-row no-gutters>
      <v-col cols="2" sm="1" class="text-center">
        <v-avatar :size="45">
          <img :src="comment.author.profile_photo_url" :alt="comment.author.name">
        </v-avatar>
      </v-col>

      <v-col  cols="10" sm="11">
        <v-row no-gutters>
          <v-col>
            <span>{{ comment.author.name }}</span>
          </v-col>
        </v-row>

        <v-row no-gutters class="mt-2">
          <v-col>
            <span class="text-subtitle-1">{{ comment.content }}</span>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
export default {
  props: {
    comment: {
      type: Object,
      required: true,
    }
  }
}
</script>