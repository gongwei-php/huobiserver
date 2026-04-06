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
import type { MemberVipVo } from '~/base/api/membervip.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

import { ElTag } from 'element-plus'
import { useMessage } from '@/hooks/useMessage.ts'
import { deleteByIds } from '~/base/api/membervip.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import hasAuth from '@/utils/permission/hasAuth.ts'

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
      cellRender: ({ row }): any => row.id === 1 ? '-' : undefined,
      selectable: (row: MemberVipVo) => ![1].includes(row.id as number),
    },
    // 索引序号列
    { type: 'index' },
    // 普通列
    { label: () => t('baseMemberVipManage.level'), prop: 'level' },
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
        type: 'tile',
        actions: [
          {
            name: 'edit',
            icon: 'material-symbols:person-edit',
            show: () => showBtn('member:vip:update'),
            text: () => t('crud.edit'),
            onClick: ({ row }) => {
              dialog.setTitle(t('crud.edit'))
              dialog.open({ formType: 'edit', data: row })
            },
          },
          {
            name: 'del',
            show: () => showBtn('member:vip:delete'),
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
