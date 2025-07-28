// src/lib/utils/toast.ts
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

export function showToast(message: string, icon: 'success' | 'error') {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: message,
        showConfirmButton: false,
        showCloseButton: true,
        timer: 3000,
        timerProgressBar: true
    });
}
