<template>
  <app-layout>
    <div class="text-center">
      <H1 class="mt-10">{{ title }}</H1>
      <div class="mt-7">{{ description }}</div>
      <inertia-link :href="route('home')">トップページに戻る</inertia-link>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

export default {
  components: {
    AppLayout,
  },
  props: {
    status: Number,
  },
  computed: {
    title() {
      return {
        503: 'サーバー利用不可',
        500: 'サーバーエラー',
        405: 'ページが見つかりません',
        404: 'ページが見つかりません',
        403: 'アクセスエラー',
      }[this.status]
    },
    description() {
      return {
        503: 'サーバーへの同時アクセス数の制限を超えているため、訪問者の閲覧を制限しています。',
        500: '申し訳ありません。現在サーバーダウンしています。',
        405: 'お探しのページは一時的にアクセスできないか、移動または削除された可能性があります。',
        404: 'お探しのページは一時的にアクセスできないか、移動または削除された可能性があります。',
        403: 'アクセスできない処理を実行しようとしています。',
      }[this.status]
    },
  },
}
</script>
