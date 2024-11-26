import Header from "./Header.jsx";
import moment from "moment";
import React, {useEffect} from "react";

function renderMessage(res) {
    if ((res.hasOwnProperty("filetype") || res.type === 'file') && ['.png', '.jpg', '.jpeg'].some(v => res.filename.includes(v))) {
        return (<img width={100} src={res.url}/>)
    } else if (res.hasOwnProperty("filetype") || res.type === 'file') {
        return (<a target={"_blank"} href={res.url}>{res.filename}</a>)
    } else {
        return (<p>{res.msg}</p>);
    }
}

export default ({message, partner, client}) => {
    return <>
        <div className={`message-item ${message.to !== client?.id && message.from !== '' ? "received" : "send"}`}>
            <div className="user-img ">
                <img
                    src={message.to !== client?.id && message.from !== '' ? partner?.avatar : client?.avatar}
                    className="lazy-img"
                />
            </div>
            <div className="message-content">
                {renderMessage(message)}
                <span className="date">{moment(message.time).format("LT")} </span>
            </div>
        </div>
    </>
};
