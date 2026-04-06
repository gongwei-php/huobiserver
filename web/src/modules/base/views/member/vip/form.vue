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
import type { MemberVipVo } from '~/base/api/membervip'
import { create, save } from '~/base/api/membervip'
import getFormItems from './data/getFormItems.tsx'
import type { MaFormExpose } from '@mineadmin/form'
import useForm from '@/hooks/useForm.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'
import useDialog from '@/hooks/useDialog.ts'

defineOptions({ name: 'member:vip:form' })
const { formType = 'add', data = null } = defineProps<{
  formType?: 'add' | 'edit'
  data?: MemberVipVo | null
}>()

const t = useTrans().globalTrans
const memberVipForm = ref<MaFormExpose>()
const memberVipModel = ref<MemberVipVo>({})

// 弹窗配置
const maDialog: UseDialogExpose = useDialog({
  lgWidth: '750px',
  ok: () => {
    maDialog.close()
  },
})

useForm('memberVipForm').then((form: MaFormExpose) => {
  if (formType === 'edit' && data) {
    Object.keys(data).map((key: string) => {
      memberVipModel.value[key] = data[key]
    })
  }
  form.setItems(getFormItems(formType, t, memberVipModel.value))
  form.setOptions({
    labelWidth: '90px',
  })
})

// 创建操作
function add(): Promise<any> {
  return new Promise((resolve, reject) => {
    create(memberVipModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    })
  })
}

// 更新操作
function edit(): Promise<any> {
  return new Promise((resolve, reject) => {
    save(memberVipModel.value.id as number, memberVipModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    })
  })
}

defineExpose({
  add,
  edit,
  maForm: memberVipForm,
})
</script>

<template>
  <div>
    <ma-form ref="memberVipForm" v-model="memberVipModel" />
  </div>
</template>

<style scoped lang="scss"></style>
