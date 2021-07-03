<template>
	<v-sheet class="mb-8">
		<v-row no-gutters>
			<v-col cols="12" md="2">
				<h3 class="text-h5">問題{{ num }}</h3>
			</v-col>

			<v-col>
				<v-card color="primary lighten-4" elevation="0">
					<v-row no-gutters class="mb-n7">
						<v-col cols="12" sm="2" class="mb-n6">
							<v-card-title>問題文</v-card-title>
						</v-col>
						<v-col>
							<v-card-text>
								<v-textarea
									solo
									dense
									:counter="255"
									:rules="[v => !!v && v.length <= 255]"
									required
									rows="3"
									v-model="emitData.question"
									@change="onChange"
								></v-textarea>
							</v-card-text>
						</v-col>
					</v-row>
					<!-- バリデーションエラー表示 -->
					<v-row v-if="getError('question')" no-gutters>
						<v-col offset-sm="2">
							<v-alert 
								color="error"
								border="left"
								dense
								class="mt-1 white--text mx-3"
								elevation="2"
							>
								{{ getError('question') }}
							</v-alert>
						</v-col>
					</v-row>

					<v-row no-gutters class="mb-n7">
						<v-col cols="12" sm="2" class="mb-n6">
							<v-card-title>回答形式</v-card-title>
						</v-col>
						<v-col sm="4">
							<v-card-text>
								<v-select
									solo
									:items="answerFormatItems"
									dense
									required
									v-model="emitData.answerFormat"
									@change="onChange"
								></v-select>
							</v-card-text>
						</v-col>
					</v-row>
					<!-- バリデーションエラー表示 -->
					<v-row v-if="getError('answerFormat')" no-gutters>
						<v-col offset-md="2">
							<v-alert 
								color="error"
								border="left"
								dense
								class="mt-n2 white--text mx-3"
								elevation="2"
							>
								{{ getError('answerFormat') }}
							</v-alert>
						</v-col>
					</v-row>

					<v-row no-gutters>
						<v-col cols="12" sm="2" class="mb-n6">
							<v-card-title>回答</v-card-title>
						</v-col>
						<v-col>
							<v-card-text>

								<div v-if="emitData.answerFormat == 1">
									<v-textarea
										solo
										dense
										required
										rows="1"
										v-model="emitData.answerText"
										@change="onChange"
									></v-textarea>
									<!-- バリデーションエラー表示 -->
									<div v-if="getError('answerText')">
										<v-alert 
											color="error"
											border="left"
											dense
											class="white--text mt-n5"
											elevation="2"
										>
											{{ getError('answerText') }}
										</v-alert>
									</div>
								</div>

								<div v-if="emitData.answerFormat == 2 || emitData.answerFormat == 3">
									<v-row no-gutters>
										<v-col cols="12" sm="4">
											<v-select
												:items="selectItemsCount(4)"
												label="選択肢数"
												outlined
												dense
												v-model="emitData.selectItemsNum"
												type="number"
												@change="onChange"
											></v-select>
											<!-- バリデーションエラー表示 -->
											<div v-if="getError('selectItemsNum')" class="ml-n16 ml-md-0 mr-md-n16">
												<v-alert 
													color="error"
													border="left"
													dense
													class="white--text mt-n5 ml-n5 ml-md-0 mr-md-n16"
													elevation="2"
												>
													{{ getError('selectItemsNum') }}
												</v-alert>
											</div>
										</v-col>

										<v-col cols="12" class="mt-n5">
											<div v-if="emitData.answerFormat == 2">
												<v-radio-group 
													required 
													v-model="emitData.answerRadio"
													@change="onChange"
												>
													<div v-for="n in emitData.selectItemsNum" :key="n">
														<v-row no-gutters align-content="center">
															<v-col cols="2" sm="1">
																<v-radio
																	class="mt-2"
																	:value="n"
																	mandatory
																></v-radio>
															</v-col>
															<v-col sm="8">
																<v-textarea
																	solo
																	dense
																	required
																	rows="1"
																	v-model="emitData.selectItemText[num2eng(n)]"
																></v-textarea>
															</v-col>
														</v-row>
														<!-- バリデーションエラー表示 -->
														<v-row 
															v-if="getSelectItemTextErrors(num2eng(n))" 
															no-gutters 
															align-content="center" 
															class="mt-n5 ml-n14 ml-md-0"
														>
															<v-col cols="12" sm="8" offset-sm="1">
																<v-alert 
																	color="error"
																	border="left"
																	dense
																	class="white--text"
																	elevation="2"
																>
																	{{ getSelectItemTextErrors(num2eng(n)) }}
																</v-alert>
															</v-col>
														</v-row>
													</div>
												</v-radio-group>
												<!-- バリデーションエラー表示 -->
												<v-row 
													v-if="getError('answerRadio')" 
													no-gutters 
													align-content="center" 
													class="mt-n5 ml-n16">
													<v-col cols="12" sm="8" offset-sm="1">
														<v-alert 
															color="error"
															border="left"
															dense
															class="white--text ml-n5"
															elevation="2"
														>
															{{ getError('answerRadio') }}
														</v-alert>
													</v-col>
												</v-row>
											</div>

											<div v-if="emitData.answerFormat == 3" class="mt-5">
												<div v-for="n in emitData.selectItemsNum" :key="n">
													<v-row no-gutters align-content="center">
														<v-col cols="2" sm="1">
															<v-checkbox
																v-model="emitData.answerCheck"
																hide-details
																multiple
																:value='n'
																class="shrink mr-4 mt-1"
																@change="onChange"
															></v-checkbox>
														</v-col>
														<v-col sm="8">
															<v-textarea
																class=""
																solo
																dense
																required
																rows="1"
																v-model="emitData.selectItemText[num2eng(n)]"
															></v-textarea>
														</v-col>
													</v-row>
													<!-- バリデーションエラー表示 -->
													<v-row 
														v-if="getSelectItemTextErrors(num2eng(n))" 
														no-gutters 
														align-content="center" 
														class="mt-n5 ml-n14 ml-md-0"
													>
														<v-col cols="12" sm="8" offset-sm="1">
															<v-alert 
																color="error"
																border="left"
																dense
																class="white--text"
																elevation="2"
															>
																{{ getSelectItemTextErrors(num2eng(n)) }}
															</v-alert>
														</v-col>
													</v-row>
												</div>
												<!-- バリデーションエラー表示 -->
												<v-row 
													v-if="getError('answerCheck')" 
													no-gutters 
													align-content="center" 
													class="mt-1 ml-n16">
													<v-col cols="12" sm="8" offset-sm="1">
														<v-alert 
															color="error"
															border="left"
															dense
															class="white--text ml-n5"
															elevation="2"
														>
															{{ getError('answerCheck') }}
														</v-alert>
													</v-col>
												</v-row>
											</div>
										</v-col>
									</v-row>
								</div>
							</v-card-text>
						</v-col>
					</v-row>
					
				</v-card>
			</v-col>
		</v-row>

	</v-sheet>
</template>

<script>
import { num2eng } from '@/util'

export default {
	props: {
		value: {
			type: Object,
			required: true,
		},
		num: {
			type: Number,
			required: true,
		},
	},
	data() {
		return {
			emitData: {
				question: this.value.question || null,
				answerFormat: this.value.answerFormat || 1,
				answerText: this.value.answerText || null,
				answerRadio: this.value.answerRadio || 1,
				answerCheck: this.value.answerCheck || [1],
				selectItemsNum: this.value.selectItemsNum || 2,
				selectItemText: this.value.selectItemText || {one:'', two:'', three:'', four:''},
			},
			answerFormatItems: [
				{ text: '記述式', value: 1 },
				{ text: '単一選択', value: 2 },
				{ text: '複数選択', value: 3 },
			],
		}
	},
	computed: {
		errors() {
			return this.$page.props.errors
		}
	},
	methods: {
		onChange() {
			this.$emit('input', this.emitData)
		},
		selectItemsCount(i) {
			return [...Array(i - 1)].map((_, i) => i + 2)
		},
    num2eng(num) {
      return num2eng(num)
    },
		getError(str) {
			return this.errors['question.' + num2eng(this.num) + '.' + str]
		},
		getSelectItemTextErrors(num) {
      return this.errors['question.' + num2eng(this.num) + '.selectItemText.' + num]
    },
	},
	watch: {
		'emitData.selectItemsNum': {
      handler() {
				// 選択肢テキストが存在しなければ復元
        for (let i = 1; i <= this.emitData.selectItemsNum; i++) {
					if (!this.emitData.selectItemText[num2eng(i)]) {
						this.emitData.selectItemText[num2eng(i)] = ''
					}
				}

				// 非表示のチェックボックスのチェックを外す
				this.emitData.answerCheck = this.emitData.answerCheck.filter(i => {
					return i <= this.emitData.selectItemsNum
				});
      },
      immediate: true,
    },
	},
}
</script>