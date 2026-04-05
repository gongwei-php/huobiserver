/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MaProTableColumns, MaProTableExpose } from '@mineadmin/pro-table'
import { type MemberAuthVo } from '~/base/api/memberauth.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

import { ElTag } from 'element-plus'
import { useMessage } from '@/hooks/useMessage.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import hasAuth from '@/utils/permission/hasAuth.ts'
import { agree, refuse } from '~/base/api/memberauth.ts'

export default function getTableColumns(dialog: UseDialogExpose, formRef: any, t: any): MaProTableColumns[] {

  const dictStore = useDictStore()
  const msg = useMessage()

  const showBtn = (auth: string | string[]) => {
    return hasAuth(auth)
  }

  return [
    // 多选列
    {
      type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection'),
      cellRender: ({ row }): any => row.id ? row.id : undefined,
      selectable: (row: MemberAuthVo) => ![1].includes(row.id as number),
    },
    { label: () => t('baseMemberAuthManage.member_id'), prop: 'member_id' },
    {
      label: () => t('baseMemberAuthManage.member_account'), prop: 'member_account',
      cellRender: ({ row }): any => row.member ? row.member.account : ''
    },
    {
      label: () => t('baseMemberAuthManage.card_front_url'), prop: 'card_front_url',
      cellRender: ({ row }) => (
        <el-image
          style="width: 100px; height: 100px"
          src={(row.card_front_url === '' || !row.card_front_url) ? '' : row.card_front_url}
          zoom-rate={1.2}
          max-scale={7}
          min-scale={0.2}
          initial-index={0}
          preview-src-list={(row.card_back_url === '' || !row.card_back_url) ? [] : [row.card_back_url]}
          show-progress
          fit="cover"
        />
      ),
    },
    {
      label: () => t('baseMemberAuthManage.card_back_url'), prop: 'card_back_url',
      cellRender: ({ row }) => (
        <el-image
          style="width: 100px; height: 100px"
          src={(row.card_back_url === '' || !row.card_back_url) ? '' : row.card_back_url}
          zoom-rate={1.2}
          max-scale={7}
          min-scale={0.2}
          initial-index={0}
          preview-src-list={(row.card_back_url === '' || !row.card_back_url) ? [] : [row.card_back_url]}
          show-progress
          fit="cover"
        />
      ),
    },
    {
      label: () => t('crud.status'), prop: 'status',
      cellRender: ({ row }) => {
        const map = {
          1: { key: 'memberauth.enums.status.wait', type: 'warning' },
          2: { key: 'memberauth.enums.status.agree', type: 'success' },
          3: { key: 'memberauth.enums.status.refuse', type: 'danger' },
        };
        const item = map[row.status] || { key: '未知', type: 'info' };
        return <ElTag type={item.type}>{t(item.key)}</ElTag>;
      }
    },
    // 操作列
    {
      type: 'operation',
      label: () => t('crud.operation'),
      operationConfigure: {
        type: 'tile',
        actions: [
          {
            name: 'agree',
            icon: 'material-symbols:person-edit',
            show: () => showBtn('member:auth:agree'),
            text: () => t('baseMemberAuthManage.agree'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('baseMemberAuthManage.agreeMessage')).then(async () => {
                const response = await agree(row.id)
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('baseMemberAuthManage.agreeSuccess'))
                  await proxy.refresh()
                }
              })
            },
          },
          {
            name: 'refuse',
            show: () => showBtn('member:auth:refuse'),
            icon: 'mdi:delete',
            text: () => t('baseMemberAuthManage.refuse'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('baseMemberAuthManage.refuseMessage')).then(async () => {
                const response = await refuse(row.id)
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('baseMemberAuthManage.refuseSuccess'))
                  await proxy.refresh()
                }
              })
            },
          },
        ],
      },
    },
  ]
}
