<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import type { MemberVo } from '~/base/api/member'
import { create, save } from '~/base/api/member'
import getFormItems from './data/getFormItems.tsx'
import type { MaFormExpose } from '@mineadmin/form'
import useForm from '@/hooks/useForm.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'
import useDialog from '@/hooks/useDialog.ts'

defineOptions({ name: 'member:form' })
const { formType = 'add', data = null } = defineProps<{
  formType?: 'add' | 'edit'
  data?: MemberVo | null
}>()

const t = useTrans().globalTrans
const memberForm = ref<MaFormExpose>()
const memberModel = ref<MemberVo>({})

// 弹窗配置
const maDialog: UseDialogExpose = useDialog({
  lgWidth: '750px',
  ok: () => {
    maDialog.close()
  },
})

useForm('memberForm').then((form: MaFormExpose) => {
  if (formType === 'edit' && data) {
    Object.keys(data).map((key: string) => {
      memberModel.value[key] = data[key]
    })
  }
  form.setItems(getFormItems(formType, t, memberModel.value))
  form.setOptions({
    labelWidth: '90px',
  })
})

// 创建操作
function add(): Promise<any> {
  return new Promise((resolve, reject) => {
    create(memberModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    })
  })
}

// 更新操作
function edit(): Promise<any> {
  return new Promise((resolve, reject) => {
    save(memberModel.value.id as number, memberModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    })
  })
}

defineExpose({
  add,
  edit,
  maForm: memberForm,
})
</script>

<template>
  <div>
    <ma-form ref="memberForm" v-model="memberModel" />
  </div>
</template>

<style scoped lang="scss"></style>
