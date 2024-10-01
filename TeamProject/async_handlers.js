function get_project_json(user_id) {
    let data = {
        user_id: user_id,
        search: document.getElementById("searchbar").value, 
        sortValue: document.getElementById("sortValue").value,
        sortOrder: document.getElementById("sortOrder").value
    }
    $.ajax({
        url: 'apis/all_projects_overview_api.php',
        method: 'GET',
        data: data,
        success: function (response) {
            console.log(response)
            display_projects(JSON.parse(response))
        },
        error: function (error) {
            console.error(error); // log error to the console
        }
    })
}

function get_user_json() {
    let data = {
        search: document.getElementById("searchbar").value, 
        sortValue: document.getElementById("sortValue").value,
        sortOrder: document.getElementById("sortOrder").value,
        filerMgr: document.getElementById("MgrToggleValue").value % 2 == 0 ? false : true,
        filerTL: document.getElementById("TLToggleValue").value % 2 == 0 ? false : true,
        filerEmp: document.getElementById("EmpToggleValue").value % 2 == 0 ? false : true
    }
    $.ajax({
        url: "apis/all_users_overview_api.php",
        method: "GET",
        data: data,
        success: function (response) {
            console.log(response)
            display_users(JSON.parse(response))
        },
        error: function(error) {
            console.log(error)
        }
    })
}

function change_sort_value(new_value) {
    document.getElementById("sortValue").value = new_value
    Array.from(document.getElementsByClassName("sortOption")).forEach(function (sortOption) {sortOption.classList.remove("bi-check")})
    document.getElementById(new_value+"ToggleIcon").classList.toggle("bi-check")
}

function change_sort_order(new_order) {
    document.getElementById("sortOrder").value = new_order
    if (new_order == "DESC") {
        document.getElementById("desc-button").classList.remove("btn-outline-secondary")
        document.getElementById("desc-button").classList.add("btn-secondary")
        document.getElementById("asc-button").classList.remove("btn-secondary")
        document.getElementById("asc-button").classList.add("btn-outline-secondary")
    } else {
        document.getElementById("asc-button").classList.remove("btn-outline-secondary")
        document.getElementById("asc-button").classList.add("btn-secondary")
        document.getElementById("desc-button").classList.remove("btn-secondary")
        document.getElementById("desc-button").classList.add("btn-outline-secondary")
    }
}

function toggleFilter(role) {
    document.getElementById(role+"ToggleValue").value++
    document.getElementById(role+"ToggleIcon").classList.toggle("bi-check")
    document.getElementById(role+"ToggleIcon").classList.toggle("bi-x")

}

function display_projects(responseJson) {
    let list_container = document.getElementById("list_container")
    list_container.innerHTML = ""
    if (responseJson["status" == "error"]) {
        list_container.innerHTML = "an error occured with your request: \n" + responseJson["message"]
        return
    } else if (responseJson["message"] == []) {
        list_container.innerHTML = "request was successful however returned no results, try changing you search paramaters."
        return
    }
    responseJson["message"].forEach(project => {
        // parent div
        let list_item = document.createElement("div")
        list_item.classList.add("list-group")

        // the link item that contains everything else
        let link_body = document.createElement("a")
        link_body.classList.add("list-group-item", "list-group-item-action", "flex-column", "align-items-start")
        link_body.href = "./new_project_analytics.php?projectToGet="+project["project_id"]

        let title_row = document.createElement("div")
        title_row.classList.add("d-flex", "w-100", "justify-content-between")
        let title = document.createElement("h5")
        title.classList.add("mb-1")
        title.innerHTML = project["project_title"]
        
        let date = document.createElement("small")
        date.innerHTML = "Due: " + project["due_date"]
        title_row.appendChild(title)
        title_row.appendChild(date)

        link_body.appendChild(title_row)
        let tl = document.createElement("h6")
        tl.innerHTML = "Project Leader: " + project["first_name"] + " " + project["surname"]
        link_body.appendChild(tl)

        let completion_p = document.createElement("p")
        completion_p.classList.add("mb-1")
        completion_p.innerHTML = (project["overall_completion"] ? project["overall_completion"]: 0) + "% complete out of " + project["task_count"] + " assigned tasks" 
        link_body.appendChild(completion_p)

        let progress_bar_containter = document.createElement("div")
        progress_bar_containter.classList.add("progress")
        progress_bar_containter.style["height"] = "1px"

        let progress_bar = document.createElement("div")
        progress_bar.role = "progressbar"
        progress_bar.classList.add("progress-bar")
        progress_bar.ariaValueNow = project["overall_completion"] ? project["overall_completion"]: 0
        progress_bar.ariaValueMin = 0
        progress_bar.ariaValueMax = 100
        progress_bar.style["width"] = project["overall_completion"] ? project["overall_completion"] + "%": 0
        
        progress_bar_containter.appendChild(progress_bar)
        link_body.appendChild(progress_bar_containter)

        let subtext = document.createElement("small")
        subtext.innerHTML = "view details"
        link_body.appendChild(subtext)
        list_item.appendChild(link_body)
        list_container.appendChild(list_item)
    });
}


function display_users(responseJson) {
    let list_container = document.getElementById("list_container")
    list_container.innerHTML = ""
    if (responseJson["status" == "error"]) {
        list_container.innerHTML = "an error occured with your request: \n" + responseJson["message"]
        return
    } else if (responseJson["message"] == []) {
        list_container.innerHTML = "request was successful however returned no results, try changing you search paramaters."
        return
    }
    responseJson["message"].forEach(user => {
        // parent div
        let list_item = document.createElement("div")
        list_item.classList.add("list-group")

        // the link item that contains everything else
        let link_body = document.createElement("a")
        link_body.classList.add("list-group-item", "list-group-item-action", "flex-column", "align-items-start")
        link_body.href = "./individual_analytics.php?userToGet="+user["user_id"]

        let title_row = document.createElement("div")
        title_row.classList.add("d-flex", "w-100", "justify-content-between")
        let title = document.createElement("h5")
        title.classList.add("mb-1")
        title.innerHTML =  user["first_name"] + " " + user["surname"]
        
        let role = document.createElement("b")
        role.appendChild(document.createElement("small"))
        switch (user["role"]) {
            case "Mgr":
                role.firstChild.innerHTML = "Manager"                
                break;
            case "TL":
                role.firstChild.innerHTML = "Team Leader"                
                break;
            case "Emp":
                role.firstChild.innerHTML = "Employee"                
                break;
            default:
                break;
        }
        title_row.appendChild(title)
        title_row.appendChild(role)
    
        link_body.appendChild(title_row)
        let email = document.createElement("h6")
        email.innerHTML = user["email"]
        link_body.appendChild(email)

        let completion_p = document.createElement("p")
        completion_p.classList.add("mb-1")
        completion_p.innerHTML = user["task_count"]+ " ongoing tasks accross " + user["project_count"] + " projects" 
        link_body.appendChild(completion_p)

        // keeping this progress bar container without the progress bar as it adds quite a nice visual element
        let progress_bar_containter = document.createElement("div")
        progress_bar_containter.classList.add("progress")
        progress_bar_containter.style["height"] = "1px"
        
        link_body.appendChild(progress_bar_containter)

        let subtext = document.createElement("small")
        subtext.innerHTML = "view details"
        link_body.appendChild(subtext)
        list_item.appendChild(link_body)
        list_container.appendChild(list_item)
    });
}
