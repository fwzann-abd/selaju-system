/**
 * LMS Real-time Listener Setup
 * Listens for Laravel Echo events and displays notifications
 */

export function setupLmsClassroomListener(classroomId) {
    if (!window.Echo) {
        console.warn('Laravel Echo is not initialized');
        return;
    }

    // Subscribe to private classroom channel
    const channel = window.Echo.private(`classroom.${classroomId}`);

    // Listen for MaterialUploaded event
    channel.listen('MaterialUploaded', (event) => {
        handleMaterialUploaded(event);
    });

    return channel;
}

/**
 * Handle when material is uploaded
 * Display toast/alert notification
 */
function handleMaterialUploaded(event) {
    const material = event.material || {};
    const teacher = event.teacher || {};

    // Show SweetAlert2 toast
    Swal.fire({
        title: 'Materi Baru! 📚',
        html: `
            <div style="text-align: left;">
                <p><strong>Guru:</strong> ${teacher.name || 'Unknown'}</p>
                <p><strong>Materi:</strong> ${material.name || 'Materi Baru'}</p>
                <p style="font-size: 0.85rem; color: #666; margin-top: 8px;">
                    Waktu: ${new Date(material.created_at).toLocaleString('id-ID')}
                </p>
            </div>
        `,
        icon: 'info',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // You can also trigger other actions here
    console.log('Material uploaded event received:', event);
}

/**
 * Setup multiple classroom listeners
 * Useful when loading multiple classrooms
 */
export function setupMultipleLmsListeners(classroomIds) {
    if (!Array.isArray(classroomIds)) {
        classroomIds = [classroomIds];
    }

    const channels = [];
    classroomIds.forEach(id => {
        channels.push(setupLmsClassroomListener(id));
    });

    return channels;
}

/**
 * Cleanup listeners for a classroom
 */
export function cleanupLmsListener(classroomId) {
    if (!window.Echo) {
        return;
    }

    window.Echo.leave(`classroom.${classroomId}`);
}

/**
 * Cleanup multiple listeners
 */
export function cleanupMultipleLmsListeners(classroomIds) {
    if (!Array.isArray(classroomIds)) {
        classroomIds = [classroomIds];
    }

    classroomIds.forEach(id => {
        cleanupLmsListener(id);
    });
}
