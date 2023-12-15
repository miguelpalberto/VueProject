const httpServer = require('http').createServer()
const io = require("socket.io")(httpServer, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
        credentials: true
    }
})
httpServer.listen(8080, () => {
    console.log('listening on *:8080')
})

const availableSupportChatRooms = []
const beingAttendedSupportChatRooms = []

io.on('connection', (socket) => {
    console.log(`client ${socket.id} has connected`);

    socket.on('loggedIn', (user) => {
        console.log(`${user.username} has logged in`)
        socket.join(user.username)
        if (user.isAdmin) {
            console.log(`${user.username} is an administrator`)
            socket.join("administrators")
        }
        socket.to(user.username).emit('requestUserLogout', {
            message: "Your account has been logged in on another device. Please contact an administrator for more information."
        })
    })

    socket.on('vCardBlocked', (vCard) => {
        console.log(`vcard #${vCard.phone_number} has been blocked`)
        io.to(vCard.phone_number).emit('requestUserLogout', {
            message: "Your vCard has been blocked. Please contact an administrator for more information."
        })
        socket.to("administrators").emit('vCardBlocked', vCard)
    })

    socket.on('vCardUnblocked', (vCard) => {
        console.log(`vcard #${vCard.phone_number} has been unblocked`)
        socket.to("administrators").emit('vCardUnblocked', vCard)
    })

    socket.on('userDeleted', (user, isAdminRequest = true) => {
        console.log(`user #${user.username} has been deleted`)
        if (isAdminRequest) {
            io.to(user.username).emit('requestUserLogout', {
                message: "Your account has been deleted. Please contact an administrator for more information."
            })
        }
        socket.to("administrators").emit('userDeleted', user)
    })

    socket.on('vcardMaxDebitChanged', (vCard) => {
        console.log(`vcard #${vCard.phone_number} max debit has been changed`)
        io.to(vCard.phone_number).emit('maxDebitChanged', vCard)
        socket.to("administrators").emit('vcardMaxDebitChanged', vCard)
    })

    socket.on('vcardProfileUpdated', (vCard) => {
        console.log(`vcard profile #${vCard.username} has been updated`)
        socket.to("administrators").emit('vcardProfileUpdated', vCard)
    })

    socket.on('adminProfileUpdated', (user) => {
        console.log(`admin profile #${user.username} has been updated`)
        socket.to("administrators").emit('adminProfileUpdated', user)
    })

    socket.on('newTransaction', (transaction) => {
        //transação normal de vcard para vcard
        if (transaction.type == "D" && transaction.payment_type == "VCARD") {
            io.to(transaction.pair_vcard).emit('newTransaction', transaction)
        }
        //transação de crédito feita por um administrador
        if (transaction.type == "C") {
            io.to(transaction.vcard).emit('newTransaction', transaction)
        }

        socket.to("administrators").emit('vCardTransactionsUpdated', transaction)
    })

    socket.on('createSupportChatroom', (supportRoom) => {
        console.log(`support chatroom #${supportRoom.name} has been created`)
        socket.join(supportRoom.name)
        socket.to("administrators").emit('createSupportChatroom', supportRoom)
        availableSupportChatRooms.push(supportRoom)
        io.emit('availableSupportChatRooms', availableSupportChatRooms)
    })

    socket.on('joinSupportChatroom', (supportRoom) => {
        console.log(`support chatroom #${supportRoom.name} has been joined by ${supportRoom.admin.username}`)
        socket.join(supportRoom.name)
        io.to(supportRoom.name).emit('joinSupportChatroom', supportRoom)
        const automaticMessage = {
            value: `Hello, ${supportRoom.vcard.name}! This is ${supportRoom.admin.name}. How can I help you today? :)`,
            sender: supportRoom.admin.name,
            timestamp: Date.now(),
            roomName: supportRoom.name
        }
        supportRoom.messages.push(automaticMessage)
        io.to(supportRoom.name).emit('supportChatroomSendMessage', automaticMessage)
        socket.to("administrators").emit('supportChatroomFull', supportRoom)
        availableSupportChatRooms.splice(availableSupportChatRooms.indexOf(supportRoom), 1)
        beingAttendedSupportChatRooms.push(supportRoom)
        io.emit('availableSupportChatRooms', availableSupportChatRooms)
    })

    socket.on('leaveSupportChatroom', (supportRoom) => {
        console.log(`support chatroom #${supportRoom.name} has been left by ${supportRoom.admin.username}`)
        socket.leave(supportRoom.name)
        io.to(supportRoom.name).emit('leaveSupportChatroom', supportRoom)
        beingAttendedSupportChatRooms.splice(beingAttendedSupportChatRooms.indexOf(supportRoom), 1)
    })

    socket.on('supportChatroomSendMessage', (message) => {
        if (beingAttendedSupportChatRooms.length > 0) {
            const room = beingAttendedSupportChatRooms.find(room => room.name == message.roomName)
            if (room) {
                room.messages.push(message)
            }
        }
        io.to(message.roomName).emit('supportChatroomSendMessage', message)
    })

    socket.on('deleteSupportChatroom', (supportRoom) => {
        console.log(`support chatroom #${supportRoom.name} has been deleted`)
        io.in(supportRoom.name).socketsLeave(supportRoom.name)

        if (availableSupportChatRooms.length > 0) {
            const room = availableSupportChatRooms.find(room => room.name == supportRoom.name)
            if (room) {
                availableSupportChatRooms.splice(availableSupportChatRooms.indexOf(room), 1)
            }
        }

        if (beingAttendedSupportChatRooms.length > 0) {
            const room = beingAttendedSupportChatRooms.find(room => room.name == supportRoom.name)
            if (room) {
                beingAttendedSupportChatRooms.splice(beingAttendedSupportChatRooms.indexOf(room), 1)
            }
        }
        io.emit('availableSupportChatRooms', availableSupportChatRooms)
    })

    setInterval(() => {
        io.emit('availableSupportChatRooms', availableSupportChatRooms)
    }, 5000)

    socket.on('disconnecting', () => {
        console.log(`client ${socket.id} has disconnecting`)
        
        const internalRooms = Array.from(socket.rooms)
        internalRooms.forEach(internalRoom => {
            if (internalRoom.includes('_supportChatroom')) {
                const availableSupportRoom = availableSupportChatRooms.find(room => room.name == internalRoom)
                if (availableSupportRoom) {
                    availableSupportChatRooms.splice(availableSupportChatRooms.indexOf(availableSupportRoom), 1)
                    io.in(internalRoom).socketsLeave(internalRoom)
                }
                else {
                    if (beingAttendedSupportChatRooms.length == 0) {
                        return
                    }
                    const beingAttendedSupportRoom = beingAttendedSupportChatRooms.find(room => room.name == internalRoom)
                    beingAttendedSupportRoom.isChatOver = true
                    io.to(internalRoom).emit('leaveSupportChatroom', beingAttendedSupportRoom)
                    io.to(internalRoom).emit('supportChatroomSendMessage', {
                        value: `This chatroom has been ended by a sudden disconnection.`,
                        sender: "System",
                        timestamp: Date.now(),
                        roomName: internalRoom
                    })
                    beingAttendedSupportChatRooms.splice(beingAttendedSupportChatRooms.indexOf(beingAttendedSupportRoom), 1)
                }
            }
        })
    })
})