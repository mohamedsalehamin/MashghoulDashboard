import React, {useEffect, useRef, useState} from "react";
import Messages from "./Chat/Messages.jsx";
import Actions from "./Chat/Actions.jsx";
import AC from 'agora-chat'
import "ion-sound/js/ion.sound.min"

ion.sound({
    sounds: [
        {name: "beer_can_opening"},
        {name: "bell_ring"},
        {name: "branch_break"},
        {name: "button_click"}
    ],

    // main config
    path: "/sounds/",
    preload: true,
    multiplay: true,
    volume: 0.9
});
export default ({partner, client, conn}) => {
    const [chatVisible, setChatVisible] = useState(true);
    const [messages, setMessages] = useState([]);

    conn?.addEventHandler("connection&message", {
        onTextMessage: (message) => {
            setMessages(() => [...messages, message]);
            ion.sound.play("bell_ring");
        },

        onFileMessage: function (message) {
            // setMessages(() => [...messages,message]);
        },
        onReceivedMessage: function (message) {
        },
        onTokenWillExpire: (params) => {
            // alert("Your session will be end soon");
        },
        onTokenExpired: (params) => {
            setChatVisible(() => !chatVisible)
        },
        onError: (error) => {
            console.log("on error", error);
        },
    });

    function sendMessage(message) {

        let option = {
            chatType: "singleChat", // Sets the chat type as single chat.
            type: "txt", // Sets the message type.
            to: client?.id, // Sets the recipient of the message with user ID.
            msg: message, // Sets the message content.
        };
        // return;
        let msg = AC.message.create(option);
        console.log(msg);
        conn
            .send(msg)
            .then((res) => {
                setMessages(() => [...messages, {...msg}]);
            })
            .catch((data) => {
                console.log(data)
                console.log("send private text fail");
            });
    }

    function sendFileMessage(file) {
        // Turn the image to a binary file.
        let _file = WebIM.utils.getFileUrl(document.getElementById("attach-doc"));
        const {url, filename, filetype, data} = _file;
        console.log(url, filename)
        let allowType = {
            jpg: true,
            jpeg: true,
            gif: true,
            png: true,
            bmp: true,
            zip: true,
            txt: true,
            doc: true,
            pdf: true,
        };
        if (_file.filetype.toLowerCase() in allowType) {
            var option = {
                // Set the message type.
                type: 'file',
                file: _file,
                // Set the username of the message receiver.
                to: client?.id,
                // Set the chat type.
                chatType: "singleChat",
                // Occurs when the file fails to be uploaded.
                onFileUploadError: function () {
                    console.log("onFileUploadError");
                },
                // Reports the progress of uploading the file.
                onFileUploadProgress: function (e) {
                    console.log(e);
                },
                // Occurs when the file is uploaded.
                onFileUploadComplete: function () {
                    console.log("onFileUploadComplete");
                },
                ext: {file_length: _file.data.size},
            };
            // Create a file message.
            var msg = WebIM.message.create(option);
            // Call send to send the file message.
            conn.send(msg).then((res) => {
                setMessages(() => [...messages, {type: "file", url, filename, filetype, data, from: ""}]);
                // console.log(localBlobFile);
                console.log(url, filename)
                console.log("Success", _file);
            }).catch((e) => {
                // Occurs when the file message fails to be sent.
                console.log("Fail");
            });
        }
    }

    if (!messages.length){
        conn.getHistoryMessages({
            targetId: client?.id,
            pageSize: 50,
            cursor: -1,
            chatType: 'singleChat',
            searchDirection: 'up',
        }).then(({messages}) => {
            setMessages(() => messages.reverse());

        }).catch((e) => {

        })
    }

    return chatVisible && (
        <>
            <div className="_messenger-body">
                <Messages messages={messages}
                          partner={partner}
                          client={client}/>
                <Actions sentMessage={sendMessage} sendFileMessage={sendFileMessage}/>

            </div>


        </>
    )
}
