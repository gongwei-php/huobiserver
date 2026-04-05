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
import type { MemberVo } from '~/base/api/member.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

import defaultAvatar from '@/assets/images/defaultAvatar.jpg'
import { ElTag } from 'element-plus'
import { useMessage } from '@/hooks/useMessage.ts'
import { deleteByIds, resetPassword } from '~/base/api/member.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import hasAuth from '@/utils/permission/hasAuth.ts'

export default function getTableColumns(dialog: UseDialogExpose, formRef: any, t: any): MaProTableColumns[] {

  console.log("dialog", dialog)
  const dictStore = useDictStore()
  const msg = useMessage()

  const showBtn = (auth: string | string[]) => {
    return hasAuth(auth)
  }

  return [
    // 多选列
    {
      type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection'),
      cellRender: ({ row }): any => row.id === 1 ? '-' : undefined,
      selectable: (row: MemberVo) => ![1].includes(row.id as number),
    },
    // 索引序号列
    { type: 'index' },
    // 普通列
    {
      label: () => t('baseMemberManage.avatar'), prop: 'avatar', width: '120px',
      cellRender: ({ row }) => (
        <div class="flex-center">
          <el-avatar src={(row.avatar === '' || !row.avatar) ? defaultAvatar : row.avatar} alt={row.account} />
        </div>
      ),
    },
    { label: () => t('baseMemberManage.account'), prop: 'account' },
    { label: () => t('baseMemberManage.phone'), prop: 'phone' },
    {
      label: () => t('crud.status'), prop: 'status',
      cellRender: ({ row }) => (
        <ElTag type={dictStore.t('system-status', row.status, 'color')}>
          {t(dictStore.t('system-status', row.status, 'i18n'))}
        </ElTag>
      ),
    },
    // 操作列
    {
      type: 'operation',
      label: () => t('crud.operation'),
      operationConfigure: {
        actions: [
          {
            name: 'edit',
            icon: 'material-symbols:person-edit',
            show: () => showBtn('member:update'),
            text: () => t('crud.edit'),
            onClick: ({ row }) => {
              dialog.setTitle(t('crud.edit'))
              dialog.open({ formType: 'edit', data: row })
            },
          },
          {
            name: 'initPassword',
            show: () => showBtn('member:password'),
            icon: 'material-symbols:passkey',
            text: () => t('baseMemberManage.initPassword'),
            onClick: ({ row }) => {
              msg.confirm(t('baseMemberManage.setPassword')).then(async () => {
                const response = await resetPassword(row.id)
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('baseMemberManage.setPasswordSuccess'))
                }
              })
            },
          },
          {
            name: 'del',
            show: () => showBtn('member:delete'),
            icon: 'mdi:delete',
            text: () => t('crud.delete'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('crud.delDataMessage')).then(async () => {
                const response = await deleteByIds([row.id])
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('crud.delSuccess'))
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
