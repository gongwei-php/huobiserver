
import type { MaFormItem } from '@mineadmin/form'
import type { MemberVo } from '~/base/api/member.ts'
import MaUploadImage from '@/components/ma-upload-image/index.vue'
import MaDictRadio from '@/components/ma-dict-picker/ma-dict-radio.vue'

export default function getFormItems(
  formType: 'add' | 'edit' = 'add',
  t: any,
  model: MemberVo,
): MaFormItem[] {
  if (formType === 'add') {
    model.password = '123456'
    model.vip_level_id = 0
    model.status = 1
  }

  if (formType === 'edit') {
    const findNode = (nodes: any[], id: number) => {
      for (let i = 0; i < nodes.length; i++) {
        if (nodes[i].id === id) {
          return nodes[i]
        }
        if (nodes[i].children) {
          const node = findNode(nodes[i].children, id)
          if (node) {
            return node
          }
        }
      }
      return null
    }
  }

  return [
    {
      label: () => t('baseMemberManage.avatar'),
      prop: 'avatar',
      render: () => MaUploadImage,
    },
    {
      label: () => t('baseMemberManage.account'),
      prop: 'account',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseMemberManage.account') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: t('baseMemberManage.account') }) }],
      },
    },
    {
      label: () => t('baseMemberManage.phone'),
      prop: 'phone',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        disabled: formType === 'edit',
        placeholder: t('form.pleaseInput', { msg: t('baseMemberManage.phone') }),
      },
      // itemProps: {
      //   rules: [{ required: true, message: t('form.requiredInput', { msg: t('baseMemberManage.phone') }) }],
      // },
    },
    {
      label: () => t('baseMemberManage.vip_level'),
      prop: 'vip_level',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        disabled: formType === 'edit',
        placeholder: t('form.pleaseInput', { msg: t('baseMemberManage.vip_level') }),
      },
      // itemProps: {
      //   rules: formType === 'add' ? [{ required: true, message: t('form.requiredInput', { msg: t('baseMemberManage.password') }) }] : [],
      // },
    },
    {
      label: () => t('baseMemberManage.password'),
      prop: 'password',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        disabled: formType === 'edit',
        placeholder: t('form.pleaseInput', { msg: t('baseMemberManage.password') }),
      },
      itemProps: {
        rules: formType === 'add' ? [{ required: true, message: t('form.requiredInput', { msg: t('baseMemberManage.password') }) }] : [],
      },
    },
    {
      label: () => t('crud.status'),
      prop: 'status',
      render: () => MaDictRadio,
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('crud.status') }),
        dictName: 'system-status',
      },
    },
  ]
}
