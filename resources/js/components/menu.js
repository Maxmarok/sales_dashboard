export const menuItems = [
    {
        label: 'Главная',
        icon: 'ri-dashboard-line',
        link: '/',
    },

    {
        label: 'Отчеты',
        icon: 'ri-table-line',
        link: '/reports',
    },

    {
        label: 'Магазины',
        icon: 'ri-menu-fill',
        subItems: [
            {
                label: 'Добавить магазин',
                link: '/reports'
            },
        ],
    }
]