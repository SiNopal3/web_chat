<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Aplikasi Chat Real-Time') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex h-[70vh]">
                
                <div class="w-1/3 border-r border-gray-200 overflow-y-auto">
                    <div class="p-4 bg-gray-800 text-white font-bold">Daftar Teman</div>
                    <ul id="user-list">
                        @foreach($users as $user)
                        <li class="p-4 border-b hover:bg-gray-100 cursor-pointer flex justify-between items-center" data-id="{{ $user->id }}">
                            <span class="font-medium text-gray-700">{{ $user->name }}</span>
                            <span id="status-{{ $user->id }}" class="w-3 h-3 bg-gray-300 rounded-full transition-colors duration-300"></span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="w-2/3 flex flex-col">
                    <div class="p-4 bg-gray-100 border-b font-bold text-gray-700" id="chat-header">
                        Pilih teman di samping untuk mulai chat...
                    </div>

                    <div class="flex-1 p-4 overflow-y-auto bg-gray-50 flex flex-col gap-3" id="chat-messages">
                        <div class="text-center text-gray-400 mt-20">Belum ada obrolan.</div>
                    </div>

                    <div class="p-4 bg-white border-t flex">
                        <input type="text" id="message-input" class="flex-1 border-gray-300 rounded-l-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik pesan..." disabled>
                        <button id="send-btn" class="bg-blue-600 text-white px-6 py-2 rounded-r-lg hover:bg-blue-700 disabled:opacity-50" disabled>Kirim</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

   <script type="module">
        const myId = {{ auth()->id() }};
        let activeUserId = null;
        let currentChannel = null;

        const chatMessages = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message-input');
        const sendBtn = document.getElementById('send-btn');
        const chatHeader = document.getElementById('chat-header');

        // 1. Fungsi untuk memunculkan balon chat di layar
        function appendMessage(message, isMine) {
            // Hapus teks "Belum ada obrolan" jika ada
            if(chatMessages.innerHTML.includes('Belum ada obrolan')) {
                chatMessages.innerHTML = '';
            }

            const div = document.createElement('div');
            // Jika chat punya kita: warna biru di kanan. Jika punya teman: warna abu di kiri
            div.className = `max-w-[70%] p-3 rounded-lg shadow-sm ${isMine ? 'bg-blue-600 text-white self-end rounded-br-none' : 'bg-white border text-gray-800 self-start rounded-bl-none'}`;
            div.textContent = message;
            
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight; // Auto-scroll ke pesan terbaru
        }

        // 2. Ketika salah satu nama teman diklik
        document.querySelectorAll('#user-list li').forEach(li => {
            li.addEventListener('click', function() {
                // Ubah warna background teman yang dipilih
                document.querySelectorAll('#user-list li').forEach(el => el.classList.remove('bg-gray-200'));
                this.classList.add('bg-gray-200'); 

                activeUserId = this.getAttribute('data-id');
                const activeUserName = this.querySelector('span:first-child').innerText;
                
                // Aktifkan kolom ketik dan ganti judul header
                chatHeader.innerText = "Chatting dengan " + activeUserName;
                messageInput.disabled = false;
                sendBtn.disabled = false;
                chatMessages.innerHTML = '<div class="text-center text-gray-400 mt-20">Memuat obrolan...</div>';

                // Ambil riwayat chat lama dari database
                axios.get('/messages/' + activeUserId).then(response => {
                    chatMessages.innerHTML = '';
                    if(response.data.length === 0) {
                        chatMessages.innerHTML = '<div class="text-center text-gray-400 mt-20">Belum ada obrolan. Mulai sapa dia!</div>';
                    }
                    response.data.forEach(msg => {
                        appendMessage(msg.message, msg.sender_id === myId);
                    });
                });

                // --- GANTI FREKUENSI RADIO (PRIVATE CHANNEL) ---
                if (currentChannel) {
                    window.Echo.leave(currentChannel); // Keluar dari ruang chat teman sebelumnya
                }
                
                // Buat nama ruangan rahasia gabungan 2 ID (diurutkan agar sama persis dengan backend)
                let ids = [myId, parseInt(activeUserId)].sort((a, b) => a - b);
                currentChannel = `chat.${ids[0]}.${ids[1]}`;

                // Masuk ke ruang chat rahasia dan dengarkan pesan masuk!
                window.Echo.private(currentChannel)
                    .listen('MessageSent', (e) => {
                        if(e.message.sender_id !== myId) { // Jika pesannya bukan dari kita sendiri
                            appendMessage(e.message.message, false);
                        }
                    });
            });
        });

        // 3. Ketika tombol kirim ditekan
        sendBtn.addEventListener('click', function() {
            let text = messageInput.value.trim();
            if(text === '' || !activeUserId) return;

            // Munculkan instan di layar kita sendiri dulu
            appendMessage(text, true);
            messageInput.value = '';

            // Kirim ke backend tanpa me-reload halaman
            axios.post('/messages/' + activeUserId, { message: text });
        });

        // 4. Biar gampang kirim pakai tombol Enter di keyboard
        messageInput.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') sendBtn.click();
        });

        // 5. RADAR ONLINE (Kode yang kemarin)
        window.Echo.join(`online-users`)
            .here((users) => {
                users.forEach(user => {
                    let indicator = document.getElementById('status-' + user.id);
                    if(indicator) indicator.classList.replace('bg-gray-300', 'bg-green-500');
                });
            })
            .joining((user) => {
                let indicator = document.getElementById('status-' + user.id);
                if(indicator) indicator.classList.replace('bg-gray-300', 'bg-green-500');
            })
            .leaving((user) => {
                let indicator = document.getElementById('status-' + user.id);
                if(indicator) indicator.classList.replace('bg-green-500', 'bg-gray-300');
            });
    </script>
</x-app-layout>