export const menuItems = [
    {
        label: 'Главная',
        icon: 'ri-dashboard-line',
        link: '/dashboard',
    },

    {
        label: 'Отчеты',
        icon: 'ri-table-line',
        link: '/dashboard/reports',
    },

    {
        label: 'Магазины',
        icon: 'ri-menu-fill',
        subItems: [
            {
                label: 'Добавить магазин',
                link: '/dashboard/stores/add'
            },
        ],
    }
]