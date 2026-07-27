import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export function useRekapFilters(props, isKantinUser) {
  const filters = ref({
    work_unit_id: props.selectedWorkUnitId,
    start_date: props.startDate,
    end_date: props.endDate,
    date: props.startDate,
    search: '',
  })

  const applyFilters = () => {
    const params = isKantinUser.value
      ? {
          work_unit_id: filters.value.work_unit_id || null,
          start_date: filters.value.date,
          end_date: filters.value.date,
          search: filters.value.search || null,
        }
      : {
          work_unit_id: filters.value.work_unit_id || null,
          start_date: filters.value.start_date,
          end_date: filters.value.end_date,
          search: filters.value.search || null,
        }

    router.get('/pengelola/rekap-penjualan', params, {
      preserveState: true,
      preserveScroll: true,
    })
  }

  const setToday = () => {
    const today = new Date().toISOString().split('T')[0]
    if (isKantinUser.value) {
      filters.value.date = today
    } else {
      filters.value.start_date = today
      filters.value.end_date = today
    }
    applyFilters()
  }

  const setThisWeek = () => {
    const now = new Date()
    const startOfWeek = new Date(now)
    startOfWeek.setDate(now.getDate() - now.getDay())
    const endOfWeek = new Date(startOfWeek)
    endOfWeek.setDate(startOfWeek.getDate() + 6)

    filters.value.start_date = startOfWeek.toISOString().split('T')[0]
    filters.value.end_date = endOfWeek.toISOString().split('T')[0]
    applyFilters()
  }

  const setThisMonth = () => {
    const now = new Date()
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1)
    const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0)

    filters.value.start_date = startOfMonth.toISOString().split('T')[0]
    filters.value.end_date = endOfMonth.toISOString().split('T')[0]
    applyFilters()
  }

  const resetFilters = () => {
    const today = new Date().toISOString().split('T')[0]

    if (isKantinUser.value) {
      filters.value = {
        work_unit_id: props.workUnits.length === 1 ? props.workUnits[0].id : null,
        date: today,
        search: '',
      }
    } else {
      filters.value = {
        work_unit_id: props.workUnits.length === 1 ? props.workUnits[0].id : null,
        start_date: today,
        end_date: today,
        search: '',
      }
    }

    applyFilters()
  }

  const isToday = computed(() => {
    const today = new Date().toISOString().split('T')[0]
    return filters.value.start_date === today && filters.value.end_date === today
  })

  const isThisWeek = computed(() => {
    const now = new Date()
    const startOfWeek = new Date(now)
    startOfWeek.setDate(now.getDate() - now.getDay())
    const endOfWeek = new Date(startOfWeek)
    endOfWeek.setDate(startOfWeek.getDate() + 6)

    const start = startOfWeek.toISOString().split('T')[0]
    const end = endOfWeek.toISOString().split('T')[0]

    return filters.value.start_date === start && filters.value.end_date === end
  })

  const isThisMonth = computed(() => {
    const now = new Date()
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1)
    const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0)

    const start = startOfMonth.toISOString().split('T')[0]
    const end = endOfMonth.toISOString().split('T')[0]

    return filters.value.start_date === start && filters.value.end_date === end
  })

  const getCurrentDateRange = (formatDateIndo) => {
    if (isKantinUser.value) {
      const date = filters.value.date || props.startDate
      return formatDateIndo(date)
    } else {
      const start = filters.value.start_date || props.startDate
      const end = filters.value.end_date || props.endDate

      if (start === end) {
        return formatDateIndo(start)
      }
      return `${formatDateIndo(start)} s/d ${formatDateIndo(end)}`
    }
  }

  return {
    filters,
    applyFilters,
    setToday,
    setThisWeek,
    setThisMonth,
    resetFilters,
    isToday,
    isThisWeek,
    isThisMonth,
    getCurrentDateRange,
  }
}
