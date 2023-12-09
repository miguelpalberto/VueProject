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

    socket.on('userDeleted', (user) => {
        console.log(`user #${user.username} has been deleted`)
        io.to(user.username).emit('requestUserLogout', {
            message: "Your account has been deleted. Please contact an administrator for more information."
        })
        socket.to("administrators").emit('userDeleted', user)
    })

    socket.on('vcardMaxDebitChanged', (vCard) => {
        console.log(`vcard #${vCard.phone_number} max debit has been changed`)
        io.to(vCard.phone_number).emit('maxDebitChanged', vCard)
        socket.to("administrators").emit('vcardMaxDebitChanged', vCard)
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

        socket.to("administrators").emit('newTransaction', transaction)
    })
})