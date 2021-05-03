<template>
	<v-sheet class="mb-8">
		<v-row no-gutters>
			<v-col cols="12" md="2">
				<h3 class="text-h5">問題{{ num }}</h3>
			</v-col>

			<v-col>
				<v-card color="primary lighten-4" elevation="0">
					<v-row no-gutters class="mb-n7">
						<v-col cols="4" md="2">
							<v-card-title>問題文</v-card-title>
						</v-col>
						<v-col>
							<v-card-text>
								<v-textarea
									solo
									dense
									required
									rows="3"
									v-model="emitData.question"
									@change="onChange"
								></v-textarea>
							</v-card-text>
						</v-col>
					</v-row>
					<!-- バリデーションエラー表示 -->
					<v-row v-if="errors.question" no-gutters>
						<v-col offset-md="2">
							<v-alert 
								color="error"
								border="left"
								dense
								class="mt-n2 white--text mx-3"
								elevation="2"
							>
								{{ errors.question }}
							</v-alert>
						</v-col>
					</v-row>

					<v-row no-gutters class="mb-n7">
						<v-col cols="5" md="2">
							<v-card-title>回答形式</v-card-title>
						</v-col>
						<v-col md="4">
							<v-card-text>
								<v-select
									solo
									:items="answerFormmatItems"
									dense
									required
									v-model="emitData.answerFormmat"
									@change="onChange"
								></v-select>
							</v-card-text>
						</v-col>
					</v-row>
					<!-- バリデーションエラー表示 -->
					<v-row v-if="errors.answerFormmat" no-gutters>
						<v-col offset-md="2">
							<v-alert 
								color="error"
								border="left"
								dense
								class="mt-n2 white--text mx-3"
								elevation="2"
							>
								{{ errors.answerFormmat }}
							</v-alert>
						</v-col>
					</v-row>

					<v-row no-gutters>
						<v-col cols="3" md="2">
							<v-card-title>回答</v-card-title>
						</v-col>
						<v-col>
							<v-card-text>

								<div v-if="emitData.answerFormmat == 1">
									<v-textarea
										solo
										dense
										required
										rows="2"
										v-model="emitData.answerText"
										@change="onChange"
									></v-textarea>
									<!-- バリデーションエラー表示 -->
									<div v-if="errors.answerText" class="ml-n16 ml-md-0">
										<v-alert 
											color="error"
											border="left"
											dense
											class="white--text mt-n5 ml-n5 ml-md-0"
											elevation="2"
										>
											{{ errors.answerText }}
										</v-alert>
									</div>
								</div>

								<div v-if="emitData.answerFormmat == 2 || emitData.answerFormmat == 3">
									<v-row no-gutters>
										<v-col cols="12" sm="4">
											<v-select
												:items="selectItemsCount(4)"
												label="選択肢数"
												outlined
												dense
												v-model="emitData.selectItemsNum"
											></v-select>
											<!-- バリデーションエラー表示 -->
											<div v-if="errors.selectItemsNum" class="ml-n16 ml-md-0 mr-md-n16">
												<v-alert 
													color="error"
													border="left"
													dense
													class="white--text mt-n5 ml-n5 ml-md-0 mr-md-n16"
													elevation="2"
												>
													{{ errors.selectItemsNum }}
												</v-alert>
											</div>
										</v-col>

										<v-col cols="12" class="mt-n5">
											<div v-if="emitData.answerFormmat == 2">
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
																<v-text-field
																	solo
																	dense
																	required
																	v-model="emitData.selectItemText[n]"
																></v-text-field>
															</v-col>
														</v-row>
														<!-- バリデーションエラー表示 -->
														<v-row 
															v-if="selectItemTextErrors(n)" 
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
																	{{ selectItemTextErrors(n) }}
																</v-alert>
															</v-col>
														</v-row>
													</div>
												</v-radio-group>
												<!-- バリデーションエラー表示 -->
												<v-row 
													v-if="errors.answerRadio" 
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
															{{ errors.answerRadio }}
														</v-alert>
													</v-col>
												</v-row>
											</div>

											<div v-if="emitData.answerFormmat == 3" class="mt-5">
												<div v-for="n in emitData.selectItemsNum" :key="n">
													<v-row no-gutters align-content="center">
														<v-col cols="2" sm="1">
															<v-checkbox
																v-model="emitData.answerCheck"
																hide-details
																multiple
																:value='n'
																class="shrink mr-4 mt-1"
															></v-checkbox>
														</v-col>
														<v-col sm="8">
															<v-text-field
																class=""
																solo
																dense
																required
																v-model="emitData.selectItemText[n]"
															></v-text-field>
														</v-col>
													</v-row>
													<!-- バリデーションエラー表示 -->
													<v-row 
														v-if="selectItemTextErrors(n)" 
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
																{{ selectItemTextErrors(n) }}
															</v-alert>
														</v-col>
													</v-row>
												</div>
												<!-- バリデーションエラー表示 -->
												<v-row 
													v-if="errors.answerCheck" 
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
															{{ errors.answerCheck }}
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
		errors: {
			type: Object,
			default() {
				return {
					question: null,
					answerFormmat: null,
					answerText: null,
					answerRadio: null,
					answerCheck: null,
					selectItemsNum: null,
					selectItemText: null,
				}
			},
		}
	},
	data() {
		return {
			emitData: {
				question: this.value.question,
				answerFormmat: this.value.answerFormmat || 1,
				answerText: this.value.answerText,
				answerRadio: this.value.answerRadio || 1,
				answerCheck: this.value.answerCheck || [1],
				selectItemsNum: this.value.selectItemsNum || 2,
				selectItemText: this.value.selectItemText || {1:'',2:'',3:'',4:''},
			},
			answerFormmatItems: [
				{ text: '記述式', value: 1 },
				{ text: '単一選択', value: 2 },
				{ text: '複数選択', value: 3 },
			],
		}
	},
	methods: {
		onChange() {
			this.$emit('input', this.emitData)
		},
		selectItemsCount(i) {
			return [...Array(i - 1)].map((_, i) => i + 2)
		},
		selectItemTextErrors(num) {
      let ret;
      try {
        ret = this.errors.selectItemText[num]
      } catch (err) {
        ret = null
      }
      return ret;
    }
	},
}
</script>