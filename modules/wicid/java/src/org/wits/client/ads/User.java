package org.wits.client.ads;

import com.extjs.gxt.ui.client.data.BaseModel;

/**
 *
 * @author Jacqueline Gil
 */

public class User extends BaseModel {

  public User(String firstname, String surname, String email) {
    set("firstname", firstname);
    set("surname", surname);
    set("email", email);
  }

  public String getFirstName() {
    return (String) get("firstname");
  }

  public String getSurname() {
    return (String) get("surname");
  }

  public String getEmail() {
    return (String) get("email");
  }

  public String toString() {
    return getFirstName()+" "+getSurname();
  }

}