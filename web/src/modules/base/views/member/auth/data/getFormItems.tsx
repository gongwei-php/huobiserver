/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MaFormItem } from '@mineadmin/form'
import type { MemberVipVo } from '~/base/api/membervip.ts'
import MaUploadImage from '@/components/ma-upload-image/index.vue'
import MaDictRadio from '@/components/ma-dict-picker/ma-dict-radio.vue'

export default function getFormItems(
  formType: 'add' | 'edit' = 'add',
  t: any,
  model: MemberVipVo,
): MaFormItem[] {
  if (formType === 'add') {
    model.level = 0
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
      label: () => t('baseMemberAuthManage.member_id'),
      prop: 'member_id',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseMemberAuthManage.member_id') }),
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
