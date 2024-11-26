export default ({username,avatar})=>{
    return <>
        <div className="chat-navbar">
            <header className="chat-header">
                <div className="d-flex align-items-center">
                    <div className="sidebar-toggle d-block d-lg-none mr-1">
                        <i data-feather="menu" className="font-medium-5"></i>
                    </div>
                    <div className="avatar avatar-border user-profile-toggle m-0 mr-1">
                        <img src={avatar}
                             alt={username} height="36" width="36"/>
                        {/*<span className="avatar-status-busy"></span>*/}
                    </div>
                    <h6 className="mb-0">{username}</h6>
                </div>
            </header>
        </div>

    </>
}
