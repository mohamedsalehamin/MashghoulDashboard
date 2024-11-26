import Header from "./Header.jsx";
import React, {useState} from "react";

export default ({sentMessage, sendFileMessage}) => {
    const [message, setMessage] = useState("");
    const [file, setFile] = useState();

    function sendChatMessage() {
        if (message === '') return;
        sentMessage(message);
        setMessage("");
    }

    function sendChatFileMessage(e) {
        console.log('change', e.target.files[0]);
        sendFileMessage(e.target.files[0]);

    }

    return <>
        <div className="message-form">
            <div className="input-content">
                <input
                    // contentEditable="true"
                    placeholder="ادخل نص الرسالة هنا"
                    className="message-input"
                    onChange={(e) => setMessage(e.target.value)}
                    onKeyDown={(e) => {
                        e.key === 'Enter' && e.target.value !== '' && sendChatMessage()
                    }}
                    value={message}
                />
                <div className="message-media">
                    <input id="attach-doc" type="file" onChange={sendChatFileMessage}/>
                    <img src="/assets/images/icons/media.svg" className="svg" />
                </div>
            </div>
            <button className="message-send" onClick={() => sendChatMessage()}>
                <img src="/assets/images/icons/send.svg" className="svg"/>
            </button>
        </div>


    </>
}
