<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Aplikasi Chat ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex h-[75vh]">
                
                <div class="w-1/3 border-r border-gray-200 flex flex-col bg-white">
                    
                    <div class="p-3 bg-gray-800 text-white font-bold text-sm">👤 DAFTAR TEMAN</div>
                    <ul id="user-list" class="overflow-y-auto max-h-[40%]">
                        @foreach($users as $user)
                        <li class="p-3 border-b hover:bg-gray-100 cursor-pointer flex justify-between items-center user-item" data-id="{{ $user->id }}">
                            <span class="font-medium text-gray-700">{{ $user->name }}</span>
                            <span id="status-{{ $user->id }}" class="w-3 h-3 bg-gray-300 rounded-full transition-colors duration-300"></span>
                        </li>
                        @endforeach
                    </ul>

                    <div class="p-3 bg-indigo-800 text-white font-bold text-sm border-t-4 border-gray-200 mt-auto">
                        👥 DAFTAR GRUP
                    </div>
                    <div class="p-2 bg-indigo-50 border-b flex gap-1">
                        <input type="text" id="new-group-name" class="flex-1 px-2 py-1 text-sm border-gray-300 rounded focus:ring-indigo-500" placeholder="Nama grup baru...">
                        <button id="btn-create-group" class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 font-bold">+</button>
                    </div>
                    <ul id="group-list" class="flex-1 overflow-y-auto bg-indigo-50/30">
                        </ul>
                </div>

                <div class="w-2/3 flex flex-col bg-gray-50">
                    <div class="p-4 bg-white border-b font-bold text-gray-800 shadow-sm flex items-center" id="chat-header">
                        Pilih teman atau grup di samping untuk mulai...
                    </div>

                    <div class="flex-1 p-4 overflow-y-auto flex flex-col gap-4" id="chat-messages">
                        <div class="text-center text-gray-400 mt-20 font-medium">Belum ada obrolan aktif.</div>
                    </div>

                    <div class="p-4 bg-white border-t flex gap-2">
                        <input type="text" id="message-input" class="flex-1 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 px-4" placeholder="Ketik pesan..." disabled>
                        <button id="send-btn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 font-bold transition-all" disabled>Kirim</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script type="module">
        const myId = {{ auth()->id() }};
        let activeId = null; 
        let activeType = null; // 'user' atau 'group'
        let currentChannel = null;

        const chatMessages = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message-input');
        const sendBtn = document.getElementById('send-btn');
        const chatHeader = document.getElementById('chat-header');
        const groupList = document.getElementById('group-list');

        // 1. Memunculkan Balon Chat (Bisa menampilkan nama pengirim untuk mode grup)
        function appendMessage(message, isMine, senderName = '') {
            if(chatMessages.innerHTML.includes('Belum ada obrolan')) chatMessages.innerHTML = '';
            
            const div = document.createElement('div');
            // Menambahkan label nama jika di grup chat dan pesannya bukan dari kita
            let label = (!isMine && senderName !== '') ? `<div class="text-xs text-indigo-200 mb-1 font-bold">${senderName}</div>` : '';
            
            div.className = `max-w-[70%] p-3 shadow-md ${isMine ? 'bg-blue-600 text-white self-end rounded-t-xl rounded-bl-xl' : 'bg-gray-800 text-gray-100 self-start rounded-t-xl rounded-br-xl'}`;
            div.innerHTML = label + `<div class="text-sm">${message}</div>`;
            
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight; 
        }

        // 2. Memuat Daftar Grup dari Database
        function loadGroups() {
            axios.get('/groups').then(response => {
                groupList.innerHTML = '';
                response.data.forEach(group => {
                    let li = document.createElement('li');
                    li.className = "p-3 border-b border-indigo-100 hover:bg-indigo-100 cursor-pointer flex justify-between items-center group-item transition-all";
                    li.innerHTML = `<span class="font-bold text-indigo-900"># ${group.name}</span>`;
                    
                    // Event saat grup diklik
                    li.onclick = () => selectChat(group.id, 'group', group.name, li);
                    groupList.appendChild(li);
                });
            });
        }
        loadGroups(); // Panggil saat web pertama kali dibuka

        // 3. Tombol Buat Grup Baru
        document.getElementById('btn-create-group').addEventListener('click', function() {
            let name = document.getElementById('new-group-name').value;
            if(name === '') return;
            axios.post('/groups', { name: name }).then(res => {
                document.getElementById('new-group-name').value = '';
                loadGroups(); // Segarkan daftar grup
            });
        });

        // 4. Fungsi Utama: Mengganti Ruang Obrolan
        function selectChat(id, type, name, element) {
            // Bersihkan warna highlight di semua menu kiri
            document.querySelectorAll('.user-item, .group-item').forEach(el => el.classList.remove('bg-gray-200', 'bg-indigo-200'));
            // Beri warna di menu yang sedang diklik
            element.classList.add(type === 'user' ? 'bg-gray-200' : 'bg-indigo-200');

            activeId = id;
            activeType = type;
            chatHeader.innerHTML = type === 'user' ? `Chat Pribadi dengan: <span class="text-blue-600 ml-1"> ${name}</span>` : `Grup Obrolan: <span class="text-indigo-600 ml-1"> # ${name}</span>`;
            
            chatMessages.innerHTML = '<div class="text-center text-gray-400 mt-20">Memuat obrolan...</div>';
            messageInput.disabled = false;
            sendBtn.disabled = false;
            messageInput.focus();

            // KELUAR dari gelombang radio sebelumnya (biar ga numpuk)
            if (currentChannel) window.Echo.leave(currentChannel);

            // LOGIKA JIKA KLIK TEMAN (PRIVATE CHAT)
            if (type === 'user') {
                axios.get('/messages/' + activeId).then(response => {
                    chatMessages.innerHTML = response.data.length ? '' : '<div class="text-center text-gray-400 mt-20">Belum ada obrolan. Sapa dia!</div>';
                    response.data.forEach(msg => appendMessage(msg.message, msg.sender_id === myId));
                });

                let ids = [myId, parseInt(activeId)].sort((a, b) => a - b);
                currentChannel = `chat.${ids[0]}.${ids[1]}`;
                
                // Mendengarkan pesan private
                window.Echo.private(currentChannel)
                    .listen('MessageSent', (e) => {
                        if(e.message.sender_id !== myId) appendMessage(e.message.message, false);
                    });
            
            // LOGIKA JIKA KLIK GRUP (GROUP CHAT)
            } else if (type === 'group') {
                // Otomatis bergabung ke grup di belakang layar
                axios.post('/groups/' + activeId + '/join').then(() => {
                    // Tarik riwayat chat grup
                    axios.get('/groups/' + activeId + '/messages').then(response => {
                        chatMessages.innerHTML = response.data.length ? '' : '<div class="text-center text-gray-400 mt-20">Grup masih sepi. Jadilah yang pertama!</div>';
                        response.data.forEach(msg => appendMessage(msg.message, msg.sender_id === myId, msg.user.name));
                    });

                    currentChannel = `group.${activeId}`;
                    
                    // Mendengarkan pesan dari PresenceChannel (Grup)
                    window.Echo.join(currentChannel)
                        .listen('GroupMessageSent', (e) => {
                            if(e.message.sender_id !== myId) appendMessage(e.message.message, false, e.message.user.name);
                        });
                });
            }
        }

        // Pasang sensor klik untuk Daftar Teman (Private)
        document.querySelectorAll('.user-item').forEach(li => {
            li.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.querySelector('span:first-child').innerText;
                selectChat(id, 'user', name, this);
            });
        });

        // 5. Mengirim Pesan (Mendeteksi dia kirim ke Teman atau ke Grup)
        sendBtn.addEventListener('click', function() {
            let text = messageInput.value.trim();
            if(text === '' || !activeId) return;

            appendMessage(text, true); // Muncul instan di layar sendiri
            messageInput.value = '';

            let url = activeType === 'user' ? '/messages/' + activeId : '/groups/' + activeId + '/messages';
            axios.post(url, { message: text });
        });

        messageInput.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') sendBtn.click();
        });

        // 6. RADAR ONLINE (Menyalakan Lampu Hijau)
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