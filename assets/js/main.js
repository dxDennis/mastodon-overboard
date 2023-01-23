const init = () => {
    $('.instance-server-visibility').prop('checked', true).change((a) => {
        $('[data-card-instance="' + $(a.currentTarget).data('instanceName') + '"]').toggle();
    })
    $('.server-card').each((i, a) => {
        let me = $(a)
        let _id = me.prop('id')
        _id = _id.replace('server-instance-', '')
        $('[data-instance-count="' + _id + '"]').text($('[data-card-instance="' + _id + '"]').length + ' Posts')
    })
    $('.trend-cards .collapse').on('show.bs.collapse', (i, a) => {
        let me = $(i.currentTarget)
        if (!me.html().length) {
            me.html('<i class="fa fa-spinner fa-pulse"></i>').load(`/mastodon/statusContext/${me.data('statusId')}/?instance=${me.data('instance')}&ajax=1`);
        }
    })
    if (typeof onReady === 'function') {
        onReady();
    }
}

$(document).ready(() => {
    init()
});
